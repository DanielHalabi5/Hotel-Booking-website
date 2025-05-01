<?php
session_start();

include('../includes/connection.php');

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

$query = "SELECT r.id, r.room_number, r.status, rt.name as room_type,
    rt.price_per_night, rt.capacity, rt.category
    FROM rooms r
    LEFT JOIN room_types rt ON r.room_type_id = rt.id
    WHERE 1=1";

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
    $roomQuery = "SELECT r.*, rt.id as room_type_id 
                  FROM rooms r
                  LEFT JOIN room_types rt ON r.room_type_id = rt.id
                  WHERE r.id = ?";
    $stmt = $conn->prepare($roomQuery);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $room = $stmt->get_result()->fetch_assoc();

    if (!$room) {
        header("Location: rooms.php");
        exit;
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

    if ($is_edit) {
        $updateQuery = "UPDATE rooms SET room_number = ?, room_type_id = ?, status = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sisi", $room_number, $room_type_id, $status, $room_id);

        if ($stmt->execute()) {
            if (isset($_POST['price_per_night'])) {
                $price_per_night = $_POST['price_per_night'];
                $updatePriceQuery = "UPDATE room_types SET price_per_night = ? WHERE id = ?";
                $priceStmt = $conn->prepare($updatePriceQuery);
                $priceStmt->bind_param("di", $price_per_night, $room_type_id);
                $priceStmt->execute();
            }
            
            $success_message = "Room updated successfully!";
        } else {
            $error_message = "Error updating room: " . $conn->error;
        }
    } else {
        $insertQuery = "INSERT INTO rooms (room_number, room_type_id, status) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("sis", $room_number, $room_type_id, $status);

        if ($stmt->execute()) {
            if (isset($_POST['price_per_night'])) {
                $price_per_night = $_POST['price_per_night'];
                $updatePriceQuery = "UPDATE room_types SET price_per_night = ? WHERE id = ?";
                $priceStmt = $conn->prepare($updatePriceQuery);
                $priceStmt->bind_param("di", $price_per_night, $room_type_id);
                $priceStmt->execute();
            }
            
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


    // Handle image upload
    $image_url = '';
    if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] == 0) {
        $upload_dir = '../uploads/rooms/';

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

// Handle room deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $room_id = $_GET['delete'];
    $deleteQuery = "DELETE FROM rooms WHERE id = ?";

    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $room_id);

    if ($stmt->execute()) {
        $success_message = "Room deleted successfully!";
        header("Location: rooms.php?deleted=1");
        exit();
    } else {
        $error_message = "Error deleting room: " . $conn->error;
    }
    $stmt->close();
}


// END ROOMS HANDLERS

// BOOKING HANDLERS

// Function to initialize booking filters
function initializeBookingFilters()
{
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
    $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'booking_date';
    $sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'DESC';

    return [
        'search' => $search,
        'status' => $status,
        'date_from' => $date_from,
        'date_to' => $date_to,
        'sort_by' => $sort_by,
        'sort_order' => $sort_order
    ];
}

// Function to get booking details
function getBookingDetails($conn, $booking_id)
{
    $bookingQuery = "SELECT b.*, u.full_name as guest_name, u.email as guest_email, 
                  u.phone as guest_phone, r.room_number, rt.name as room_type,
                  rt.description as room_description, rt.price_per_night, 
                  rt.capacity, rt.room_size, rt.category
                  FROM bookings b
                  LEFT JOIN users u ON b.user_id = u.id
                  LEFT JOIN rooms r ON b.room_id = r.id
                  LEFT JOIN room_types rt ON r.room_type_id = rt.id
                  WHERE b.id = ?";

    $stmt = $conn->prepare($bookingQuery);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}


// Function to get booking list with filters
function getBookings($conn, $search = '', $status = '', $date_from = '', $date_to = '', $sort_by = 'booking_date', $sort_order = 'DESC')
{
    $query = "SELECT b.id, u.full_name as guest_name, 
            r.room_number, rt.name as room_type, b.check_in, b.check_out, 
            b.total_price, b.booking_status, b.booking_date
            FROM bookings b
            LEFT JOIN users u ON b.user_id = u.id
            LEFT JOIN rooms r ON b.room_id = r.id
            LEFT JOIN room_types rt ON r.room_type_id = rt.id
            WHERE 1=1";

    $params = [];
    $types = "";

    if (!empty($search)) {
        $query .= " AND (b.id LIKE ? OR u.full_name LIKE ? OR 
                      r.room_number LIKE ? OR rt.name LIKE ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= "ssss";
    }

    if (!empty($status)) {
        $query .= " AND b.booking_status = ?";
        $params[] = $status;
        $types .= "s";
    }

    if (!empty($date_from)) {
        $query .= " AND b.check_in >= ?";
        $params[] = $date_from;
        $types .= "s";
    }

    if (!empty($date_to)) {
        $query .= " AND b.check_in <= ?";
        $params[] = $date_to;
        $types .= "s";
    }

    $query .= " ORDER BY $sort_by";

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result();
} 

// Function to update booking status
function updateBookingStatus($conn, $booking_id, $new_status)
{
    $updateQuery = "UPDATE bookings SET booking_status = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("si", $new_status, $booking_id);
    return $updateStmt->execute();
}

// Function to handle booking status update
function handleBookingStatusUpdate($conn)
{
    if (isset($_POST['update_status']) && isset($_POST['booking_id']) && isset($_POST['status'])) {
        $booking_id = $_POST['booking_id'];
        $new_status = $_POST['status'];

        if (updateBookingStatus($conn, $booking_id, $new_status)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
        exit;
    }
    return false;
}

// Process booking form processing
function processBookingForm()
{
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $booking_id = $_GET['id'];

        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            header('Location: bookings.php');
            exit;
        }

        global $conn;

        $booking = getBookingDetails($conn, $booking_id);

        if (!$booking) {
            header('Location: bookings.php');
            exit;
        }

        // Update booking status if form submitted
        if (isset($_POST['update_status'])) {
            $new_status = $_POST['booking_status'];

            if (updateBookingStatus($conn, $booking_id, $new_status)) {
                $success_message = "Booking status updated successfully!";

                $booking = getBookingDetails($conn, $booking_id);
            } else {
                $error_message = "Error updating booking status: " . $conn->error;
            }
        }

        $check_in = new DateTime($booking['check_in']);
        $check_out = new DateTime($booking['check_out']);
        $duration = $check_in->diff($check_out)->days;

        
        return [
            'booking' => $booking,
            'duration' => $duration,
            'success_message' => isset($success_message) ? $success_message : null,
            'error_message' => isset($error_message) ? $error_message : null
        ];
    }

    return null;
}

// END BOOKINGS HANDLERS
?>