<?php

include('../includes/connection.php');
include("admin-handlers.php");

// ROOMS HANDLERS

$room_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_edit = $room_id > 0;
$is_room_type = isset($_GET['type']) && $_GET['type'] === 'new';


$roomTypesQuery = "SELECT id, name FROM room_types ORDER BY name ASC";
$roomTypesResult = $conn->query($roomTypesQuery);

// Initialize search/filter variables
$search = isset($_GET['search']) ? $_GET['search'] : '';
$room_type = isset($_GET['room_type']) ? $_GET['room_type'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

$query = "SELECT r.id, r.room_number, r.status, r.discount_percentage, rt.name as room_type,
    rt.price_per_night, rt.capacity, rt.category,
    CASE
        WHEN r.discount_percentage > 0 
                THEN rt.price_per_night * (1 - r.discount_percentage/100) 
                ELSE rt.price_per_night 
    END as discounted_price
    FROM rooms r
    LEFT JOIN room_types rt ON r.room_type_id = rt.id
    WHERE r.deleted_at IS NULL";

$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND (r.room_number LIKE ? OR rt.name LIKE ? OR rt.category LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

if (!empty($room_type)) {
    $query .= " AND rt.id = ?";
    $params[] = $room_type;
    $types .= "i";
}

if (!empty($status)) {
    $query .= " AND r.status = ?";
    $params[] = $status;
    $types .= "s";
}

$query .= " ORDER BY r.room_number ASC";


$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$roomTypesQuery = "SELECT id, name FROM room_types ORDER BY name ASC";
$roomTypesResult = $conn->query($roomTypesQuery);

// If editing existing room, fetch data
if ($is_edit && !$is_room_type) {
    $roomQuery = "SELECT r.*, rt.id as room_type_id,
                    rt.price_per_night,
                    CASE
                        WHEN r.discount_percentage > 0 
                            THEN rt.price_per_night * (1 - r.discount_percentage/100) 
                            ELSE rt.price_per_night 
                    END as discounted_price
                    FROM rooms r
                    LEFT JOIN room_types rt ON r.room_type_id = rt.id
                    WHERE r.id = ? AND r.deleted_at IS NULL";
    $stmt = $conn->prepare($roomQuery);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $room = $stmt->get_result()->fetch_assoc();

    if (!$room) {
        header("Location: rooms.php");
        exit;
    }
    if (isset($room['price_per_night']) && isset($room['discount_percentage']) && $room['discount_percentage'] > 0) {
        $room['discounted_price'] = $room['price_per_night'] * (1 - $room['discount_percentage'] / 100);
    } else {
        $room['discounted_price'] = $room['price_per_night'];
    }
}

// If editing room type, fetch data
if ($is_room_type && $room_id > 0) {
    $roomTypeQuery = "SELECT * FROM room_types WHERE id = ?";
    $stmt = $conn->prepare($roomTypeQuery);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $roomType = $stmt->get_result()->fetch_assoc();

    if (!$roomType) {
        header("Location: rooms.php");
        exit;
    }
}

// Room form submission
if (isset($_POST['submit_room'])) {
    $room_number = $_POST['room_number'];
    $room_type_id = $_POST['room_type_id'];
    $status = $_POST['status'];
    $discount_percentage = isset($_POST['discount_percentage']) ? floatval($_POST['discount_percentage']) : 0;

    $discount_percentage = max(0, min(100, $discount_percentage));

  if ($is_edit) {
        $updateQuery = "UPDATE rooms SET room_number = ?, room_type_id = ?, status = ?, discount_percentage = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sisdi", $room_number, $room_type_id, $status, $discount_percentage, $room_id);

        if ($stmt->execute()) {
            $success_message = "Room updated successfully!";

            // Refresh room data after update
            $roomQuery = "SELECT r.*, rt.id as room_type_id, rt.price_per_night, 
                        rt.price_per_night * (1 - r.discount_percentage/100) as discounted_price
                        FROM rooms r
                        LEFT JOIN room_types rt ON r.room_type_id = rt.id
                        WHERE r.id = ? AND r.deleted_at IS NULL";
            $stmt = $conn->prepare($roomQuery);
            $stmt->bind_param("i", $room_id);
            $stmt->execute();
            $room = $stmt->get_result()->fetch_assoc();
        } else {
            $error_message = "Error updating room: " . $conn->error;
        }
    } else {
        $insertQuery = "INSERT INTO rooms (room_number, room_type_id, status, discount_percentage) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("sisd", $room_number, $room_type_id, $status, $discount_percentage);

        if ($stmt->execute()) {
            $success_message = "Room added successfully!";
            $room = null;
        } else {
            $error_message = "Error adding room: " . $conn->error;
        }
    }
}

// Room type form submission
if (isset($_POST['submit_room_type'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price_per_night = $_POST['price_per_night'];
    $capacity = $_POST['capacity'];
    $room_size = $_POST['room_size'];
    $category = $_POST['category'];

    $image_url = '';
    if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] == 0) {
        $upload_dir = '../../image/upload/room';

        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = time() . '_' . basename($_FILES['room_image']['name']);
        $target_file = $upload_dir . $file_name;

        $check = getimagesize($_FILES['room_image']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['room_image']['tmp_name'], $target_file)) {
                $image_url = 'uploads/rooms/' . $file_name;
            } else {
                $error_message = "Error uploading file.";
            }
        } else {
            $error_message = "File is not an image.";
        }
    } else if ($is_room_type && $room_id > 0 && empty($_FILES['room_image']['name'])) {
        $image_url = $roomType['image_url'];
    }

    if ($is_room_type && $room_id > 0) {
        $updateQuery = "UPDATE room_types SET name = ?, description = ?, price_per_night = ?, 
                        capacity = ?, room_size = ?, category = ?";

        $params = [$name, $description, $price_per_night, $capacity, $room_size, $category];
        $types = "ssdiis";

        if (!empty($image_url)) {
            $updateQuery .= ", image_url = ?";
            $params[] = $image_url;
            $types .= "s";
        }

        $updateQuery .= " WHERE id = ?";
        $params[] = $room_id;
        $types .= "i";

        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $success_message = "Room type updated successfully!";
        } else {
            $error_message = "Error updating room type: " . $conn->error;
        }
    } else {
        // Insert new room type
        $insertQuery = "INSERT INTO room_types (name, description, price_per_night, capacity, 
                        room_size, category, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ssdisss", $name, $description, $price_per_night, $capacity, $room_size, $category, $image_url);

        if ($stmt->execute()) {
            $success_message = "Room type added successfully!";
            $roomType = null;
        } else {
            $error_message = "Error adding room type: " . $conn->error;
        }
    }
}


// Handle soft delete
function handleRoomSoftDelete($conn)
{
    if (isset($_GET['soft_delete']) && is_numeric($_GET['soft_delete']) && isset($_GET['type']) && $_GET['type'] == 'room') {
        $room_id = $_GET['soft_delete'];
        $current_time = date('Y-m-d H:i:s');

        $softDeleteQuery = "UPDATE rooms SET deleted_at = ? WHERE id = ?";

        $stmt = $conn->prepare($softDeleteQuery);
        $stmt->bind_param("si", $current_time, $room_id);

        if ($stmt->execute()) {
            $success_message = "Room marked as deleted successfully!";
            header("Location: rooms.php?deleted=1");
            exit();
        } else {
            $error_message = "Error marking room as deleted: " . $conn->error;
        }
        $stmt->close();
    }
}