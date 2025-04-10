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

    // Add sorting
    $query .= " ORDER BY $sort_by";

    // Prepare and execute query
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result();
}

// END BOOKINGS HANDLERS
?>
