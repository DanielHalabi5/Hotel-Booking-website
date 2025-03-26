<?php
session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get credentials from form
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic validation
    if (empty($email) || empty($password)) {
        $_SESSION['response'] = [
            'success' => false,
            'message' => 'Please enter both email and password'
        ];
        header('Location: ');
        exit;
    }

    // Connect to database
    require_once('connection.php');

    // Query to check user credentials and get position
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $stored_password = $user['password'];

        // Verify password using PHP's password_verify function
        if (password_verify($password, $stored_password)) {
            // Authentication successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_position'] = $user['position'];
            $_SESSION['logged_in'] = true;
        }
    }

    // If we get here, authentication failed
    $_SESSION['response'] = [
        'success' => false,
        'message' => 'Invalid email or password'
    ];
    header('Location: ../login.php');
    exit;
}
