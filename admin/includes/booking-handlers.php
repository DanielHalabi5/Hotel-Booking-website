<?php

    include("../includes/connection.php");
    include("admin-handlers.php");


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
                    u.phone as guest_phone, r.room_number, r.discount_percentage, 
                    rt.name as room_type, rt.description as room_description, 
                    rt.price_per_night, rt.capacity, rt.room_size, rt.category,
                    CASE
                        WHEN r.discount_percentage > 0 
                            THEN rt.price_per_night * (1 - r.discount_percentage/100) 
                            ELSE rt.price_per_night 
                    END as discounted_price_per_night
                    FROM bookings b
                    LEFT JOIN users u ON b.user_id = u.id
                    LEFT JOIN rooms r ON b.room_id = r.id
                    LEFT JOIN room_types rt ON r.room_type_id = rt.id
                    WHERE b.id = ? ";

    $stmt = $conn->prepare($bookingQuery);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}


// Function to get booking list with filters
function getBookings($conn, $search = '', $status = '', $date_from = '', $date_to = '', $sort_by = 'booking_date', $sort_order = 'DESC')
{
    $query = "SELECT b.id, u.full_name as guest_name, 
            r.room_number, r.discount_percentage, rt.name as room_type, 
            rt.price_per_night, b.check_in, b.check_out, 
            b.total_price, b.booking_status, b.booking_date,
            CASE
                WHEN r.discount_percentage > 0 
                    THEN rt.price_per_night * (1 - r.discount_percentage/100) 
                    ELSE rt.price_per_night 
            END as discounted_price_per_night
            FROM bookings b
            LEFT JOIN users u ON b.user_id = u.id
            LEFT JOIN rooms r ON b.room_id = r.id
            LEFT JOIN room_types rt ON r.room_type_id = rt.id";

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

    $query .= " ORDER BY $sort_by $sort_order";

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
        
        global $conn;
        
        $booking = getBookingDetails($conn, $booking_id);
        
        if ($booking) {
            $check_in = new DateTime($booking['check_in']);
            $check_out = new DateTime($booking['check_out']);
            $duration = $check_in->diff($check_out)->days;
            
            $success_message = null;
            $error_message = null;
            
            if (isset($_POST['update_status'])) {
                $new_status = $_POST['booking_status'];
                
                if (updateBookingStatus($conn, $booking_id, $new_status)) {
                    $success_message = "Booking status updated successfully!";
                    
                    $booking = getBookingDetails($conn, $booking_id);
                } else {
                    $error_message = "Error updating booking status: " . $conn->error;
                }
            }
            
            return [
                'booking' => $booking,
                'duration' => $duration,
                'success_message' => isset($success_message) ? $success_message : null,
                'error_message' => isset($error_message) ? $error_message : null
            ];
        } 
    }
    
    return null;
}

function autoUpdateBookingStatuses($conn) {
    $today = date('Y-m-d');
$update_query = "UPDATE bookings 
                SET booking_status = 'completed' 
                WHERE booking_status = 'confirmed' 
                AND check_out < '$today' 
                AND deleted_at IS NULL";

if ($conn->query($update_query)) {
    $updated_count = $conn->affected_rows;
    echo "Updated $updated_count expired confirmed bookings to completed status.<br>";
} else {
    echo "Error updating booking statuses: " . $conn->error . "<br>";
}

}