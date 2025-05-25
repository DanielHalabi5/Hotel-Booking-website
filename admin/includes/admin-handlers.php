<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['position'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['position'] !== 'admin' && $_SESSION['position'] !== 'manager') {
    header("Location: ../login.php?error=unauthorized");
    exit();
}

$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Admin User';
$user_id = $_SESSION['user_id'];
$user_position = $_SESSION['position'];