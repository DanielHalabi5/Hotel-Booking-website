<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];
$user_email = $_SESSION['user_email'] ?? '';

$active_section = isset($_GET['section']) && $_GET['section'] === 'bookings' ? 'bookings' : 'profile';

require_once 'connection.php';

$update_success = false;
$update_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_name = trim($_POST['name']);
    $new_email = trim($_POST['email']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($new_name) || empty($new_email)) {
        $update_error = "Name and email required";
    } else {
        if (!empty($new_password)) {
            if (empty($current_password)) {
                $update_error = "Current password required to change password";
            } elseif ($new_password !== $confirm_password) {
                $update_error = "New passwords don't match";
            } else {
                $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user_data = $result->fetch_assoc();
                
                if (password_verify($current_password, $user_data['password'])) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
                    $stmt->bind_param("sssi", $new_name, $new_email, $hashed_password, $user_id);
                    if ($stmt->execute()) {
                        $update_success = true;
                        $_SESSION['full_name'] = $new_name;
                        $_SESSION['user_email'] = $new_email;
                        $full_name = $new_name;
                        $user_email = $new_email;
                    } else {
                        $update_error = "Update failed";
                    }
                } else {
                    $update_error = "Incorrect password";
                }
            }
        } else {
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $new_name, $new_email, $user_id);
            if ($stmt->execute()) {
                $update_success = true;
                $_SESSION['full_name'] = $new_name;
                $_SESSION['user_email'] = $new_email;
                $full_name = $new_name;
                $user_email = $new_email;
            } else {
                $update_error = "Update failed";
            }
        }
    }
}

$bookings = [];

$columnsQuery = "SHOW COLUMNS FROM rooms LIKE 'number'";
$columnsResult = $conn->query($columnsQuery);
$numberColumnExists = $columnsResult->num_rows > 0;

if ($numberColumnExists) {
    $stmt = $conn->prepare("SELECT b.*, r.number as room_number FROM bookings b 
                            LEFT JOIN rooms r ON b.room_id = r.id
                            WHERE b.user_id = ? ORDER BY b.booking_date DESC");
} else {
    $stmt = $conn->prepare("SELECT b.* FROM bookings b 
                            WHERE b.user_id = ? ORDER BY b.booking_date DESC");
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

$conn->close();
?>