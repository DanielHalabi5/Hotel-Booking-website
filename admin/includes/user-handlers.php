<?php

include('../includes/connection.php');

// Function to get users list with filters
function getUsers($conn, $search = '', $role = '', $sort_by = 'id', $sort_order = 'ASC')
{
    $query = "SELECT id, full_name, email, phone, position, created_at,
    last_login FROM users WHERE deleted_at IS NULL";

    $params = [];
    $types = "";

    if (!empty($search)) {
        $query .= " AND (full_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= "sss";
    }

    if (!empty($role)) {
        $query .= " AND position = ?";
        $params[] = $role;
        $types .= "s";
    }

    // Add sorting
    $query .= " ORDER BY $sort_by $sort_order";

    // Prepare and execute query
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result();
}

// Function to get user details by ID
function getUserDetails($conn, $user_id)
{
    $query = "SELECT * FROM users WHERE id = ? AND deleted_at IS NULL";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Function to add new user
function addUser($conn, $userData)
{
    // Hash password
    $password_hash = password_hash($userData['password'], PASSWORD_DEFAULT);

    $query = "INSERT INTO users (full_name, email, phone, password, position)
    VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "sssss",
        $userData['full_name'],
        $userData['email'],
        $userData['phone'],
        $password_hash,
        $userData['position']
    );

    return $stmt->execute();
}

function updateUser($conn, $user_id, $userData)
{
    $query = "UPDATE users SET full_name = ?, email = ?, phone = ?,
    position = ?";

    $params = [
        $userData['full_name'],
        $userData['email'],
        $userData['phone'],
        $userData['position'],
    ];

    $types = "ssss";

    if (!empty($userData['password'])) {
        $password_hash = password_hash($userData['password'], PASSWORD_DEFAULT);
        $query .= ", password = ?";
        $params[] = $password_hash;
        $types .= "s";
    }

    $query .= " WHERE id = ?";
    $params[] = $user_id;
    $types .= "i";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);

    return $stmt->execute();
}

// Function to soft delete a user
function softDeleteUser($conn, $user_id)
{
    $current_time = date('Y-m-d H:i:s');
    $softDeleteQuery = "UPDATE users SET deleted_at = ? WHERE id = ?";

    $stmt = $conn->prepare($softDeleteQuery);
    $stmt->bind_param("si", $current_time, $user_id);

    return $stmt->execute();
}

function initializeUsersView($conn)
{
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $role = isset($_GET['role']) ? $_GET['role'] : '';
    $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'id';
    $sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';

    $success_message = null;
    $error_message = null;

    // Handle soft delete
    if (isset($_GET['soft_delete']) && is_numeric($_GET['soft_delete'])) {
        $user_id = $_GET['soft_delete'];

        if (softDeleteUser($conn, $user_id)) {
            $success_message = "User marked as deleted successfully!";

            $redirect_url = 'users-view.php?deleted=1';
            if (!empty($search)) $redirect_url .= '&search=' . urlencode($search);
            if (!empty($role)) $redirect_url .= '&role=' . urlencode($role);
            if (!empty($sort_by)) $redirect_url .= '&sort_by=' . urlencode($sort_by);
            if (!empty($sort_order)) $redirect_url .= '&sort_order=' . urlencode($sort_order);

            header("Location: " . $redirect_url);
            exit();
        } else {
            $error_message = "Error marking user as deleted: " . $conn->error;
        }
    }

    if (isset($_GET['deleted']) && $_GET['deleted'] == '1') {
        $success_message = "User marked as deleted successfully!";
    }

    $result = getUsers($conn, $search, $role, $sort_by, $sort_order);

    return [
        'search' => $search,
        'role' => $role,
        'sort_by' => $sort_by,
        'sort_order' => $sort_order,
        'result' => $result,
        'success_message' => $success_message,
        'error_message' => $error_message
    ];
}

function processUserForm()
{
    // Check if we're editing a user
    $user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $is_edit = $user_id > 0;

    global $conn;
    $user = null;
    $success_message = null;
    $error_message = null;

    // If editing, fetch user data
    if ($is_edit) {
        $user = getUserDetails($conn, $user_id);

        if (!$user) {
            header("Location: users-view.php");
            exit;
        }
    }

    // Process form submission
    if (isset($_POST['action']) && ($_POST['action'] == 'add_user' || $_POST['action'] == 'update_user')) {
        $userData = [
            'full_name' => $_POST['full_name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'position' => $_POST['position'],
            'password' => isset($_POST['password']) ? $_POST['password'] : ''
        ];


        // Validate data
        $is_valid = true;

        // Email validation
        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $error_message = "Invalid email format";
            $is_valid = false;
        }

        // Check for duplicate email
        $emailCheckQuery = "SELECT id FROM users WHERE email = ? AND id != ?";
        $emailStmt = $conn->prepare($emailCheckQuery);
        $emailStmt->bind_param("si", $userData['email'], $user_id);
        $emailStmt->execute();
        $emailResult = $emailStmt->get_result();

        if ($emailResult->num_rows > 0) {
            $error_message = "Email already exists in the system";
            $is_valid = false;
        }

        // For new users, password is required
        if (!$is_edit && empty($userData['password'])) {
            $error_message = "Password is required for new users";
            $is_valid = false;
        }

        if ($is_valid) {
            if ($is_edit) {
                // Update existing user
                if (updateUser($conn, $user_id, $userData)) {
                    $success_message = "User updated successfully!";
                    $user = getUserDetails($conn, $user_id); // Refresh user data
                } else {
                    $error_message = "Error updating user: " . $conn->error;
                }
            } else {
                // Create new user
                if (addUser($conn, $userData)) {
                    $success_message = "User added successfully!";
                    // Clear form after successful addition
                    $user = null;
                } else {
                    $error_message = "Error adding user: " . $conn->error;
                }
            }
        }
    }

    return [
        'user' => $user,
        'is_edit' => $is_edit,
        'success_message' => $success_message,
        'error_message' => $error_message
    ];
}

function getUserBookings($conn, $user_id) {
    $bookings = [];
    
    // First check if 'number' column exists in the rooms table
    $columnsQuery = "SHOW COLUMNS FROM rooms LIKE 'number'";
    $columnsResult = $conn->query($columnsQuery);
    $numberColumnExists = $columnsResult->num_rows > 0;
    
    if ($numberColumnExists) {
        // If 'number' column exists, use it
        $stmt = $conn->prepare("SELECT b.*, r.number as room_number FROM bookings b 
                                LEFT JOIN rooms r ON b.room_id = r.id
                                WHERE b.user_id = ? ORDER BY b.booking_date DESC");
    } else {
        // If 'number' column doesn't exist, use room_id directly
        $stmt = $conn->prepare("SELECT b.* FROM bookings b 
                                WHERE b.user_id = ? ORDER BY b.booking_date DESC");
    }
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    
    return $bookings;
}
