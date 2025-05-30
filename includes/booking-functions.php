
<?php
require_once('connection.php');

function getRoomTypes($conn)
{
    $sql = "SELECT id, name, description, price_per_night, capacity FROM room_types ORDER BY name";
    $result = mysqli_query($conn, $sql);
    $room_types = [];

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $room_types[] = $row;
        }
    }

    return $room_types;
}

function getAvailableRooms($conn, $room_type_id, $check_in, $check_out)
{
    $sql = "SELECT r.id, r.room_number 
            FROM rooms r 
            WHERE r.room_type_id = ? 
            AND r.status = 'available' 
            AND r.deleted_at IS NULL
            AND r.id NOT IN (
                SELECT b.room_id 
                FROM bookings b 
                WHERE b.booking_status IN ('pending', 'confirmed') 
                AND ((b.check_in <= ? AND b.check_out > ?) 
                     OR (b.check_in < ? AND b.check_out >= ?))
            )";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "issss", $room_type_id, $check_in, $check_in, $check_out, $check_out);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $available_rooms = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $available_rooms[] = $row;
    }

    return $available_rooms;
}

function processBooking($conn, $booking_data)
{
    $user_id = $_SESSION['user_id'];
    $room_type_id = (int)$booking_data['room_id'];
    $check_in = $booking_data['arriveDate'];
    $check_out = $booking_data['departDate'];
    $guests = (int)$booking_data['guests'];

    $check_in_date = new DateTime($check_in);
    $check_out_date = new DateTime($check_out);
    $nights = $check_in_date->diff($check_out_date)->days;

    $sql = "SELECT price_per_night, capacity FROM room_types WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $room_type_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $room_type = mysqli_fetch_assoc($result);

    $total_price = $nights * $room_type['price_per_night'];

    $available_rooms = getAvailableRooms($conn, $room_type_id, $check_in, $check_out);

    if (empty($available_rooms)) {
        return ['success' => false, 'message' => 'No rooms available for selected dates.'];
    }

    $room_id = $available_rooms[0]['id'];

    $sql = "INSERT INTO bookings (user_id, room_id, check_in, check_out, guests, total_price, booking_status) 
            VALUES (?, ?, ?, ?, ?, ?, 'pending')";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iissid", $user_id, $room_id, $check_in, $check_out, $guests, $total_price);

    if (mysqli_stmt_execute($stmt)) {
        $booking_id = mysqli_insert_id($conn);
        header("Location: booking-confirmation.php?id=$booking_id");
        exit;
    } else {
        return ['success' => false, 'message' => 'Error creating booking: ' . mysqli_error($conn)];
    }
}

$room_types = getRoomTypes($conn);
$selected_room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : '';
$booking_result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_result = processBooking($conn, $_POST);
}
?>