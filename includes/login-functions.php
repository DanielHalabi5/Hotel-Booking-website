<?php
session_start();
require_once('connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['fullname'])) {
        $fullname = trim($_POST['fullname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $phone = trim($_POST['phone'] ?? '');

        if (empty($fullname) || empty($email) || empty($password)) {
            $_SESSION['response'] = [
                'success' => false,
                'message' => 'Please fill all required fields'
            ];
            header('Location: ../signup.php');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['response'] = [
                'success' => false,
                'message' => 'Please enter a valid email address'
            ];
            header('Location: ../signup.php');
            exit;
        }

        if ($password !== $confirm_password) {
            $_SESSION['response'] = [
                'success' => false,
                'message' => 'Passwords do not match'
            ];
            header('Location: ../signup.php');
            exit;
        }

        $check_sql = "SELECT * FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['response'] = [
                'success' => false,
                'message' => 'Email already exists. Please use a different email.'
            ];
            header('Location: ../signup.php');
            exit;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (full_name, email, password, phone) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $fullname, $email, $hashed_password, $phone);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $_SESSION['response'] = [
                'success' => true,
                'message' => 'Account created successfully! Please login.'
            ];
            header('Location: ../login.php');
        } else {
            $_SESSION['response'] = [
                'success' => false,
                'message' => 'Registration failed. Please try again.'
            ];
            header('Location: ../signup.php');
        }
        exit;

    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['response'] = [
                'success' => false,
                'message' => 'Please enter both email and password'
            ];
            header('Location: ../login.php');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['response'] = [
                'success' => false,
                'message' => 'Please enter a valid email address'
            ];
            header('Location: ../login.php');
            exit;
        }

        $sql = "SELECT * FROM users WHERE email = ? AND deleted_at IS NULL";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['position'] = $user['position'];
                $_SESSION['logged_in'] = true;

                if ($user['position'] == "admin") {
                    header('Location: ../admin/dashboard.php');
                } else {
                    header('Location: ../index.php');
                }
                exit;
            }
        }

        $_SESSION['response'] = [
            'success' => false,
            'message' => 'Invalid email or password'
        ];
        header('Location: ../login.php');
        exit;
    }

} else {
    header('Location: ../login.php');
    exit;
}
?>