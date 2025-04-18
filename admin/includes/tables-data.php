<?php
session_start();

include('../includes/connection.php');

// ROOMS HANDLERS


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