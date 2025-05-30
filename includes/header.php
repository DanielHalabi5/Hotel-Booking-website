<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$full_name = $logged_in ? $_SESSION['full_name'] : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vellora Hotel</title>
    <link rel="stylesheet" type="text/css" href="css/style.css?v=<?= time() ?>">
    <script src="js/main.js?v=<?= time() ?>"></script>
    <script src="https://kit.fontawesome.com/354cef8def.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="header">
        <div class="nav">
            <h2> Vellora </h2>
            <a href="index.php">Home</a>
            <a href="index.php#about-section">About Us</a>
            <a href="index.php#contact-section">Contact Us</a>

            <?php if ($logged_in): ?>
                <div class="user-menu">
                    <a href="javascript:void(0);" class="user-link"><?php echo htmlspecialchars($full_name); ?></a>
                    <div class="user-dropdown">
                        <a href="user-profile.php?section=profile">My Profile</a>
                        <a href="user-profile.php?section=bookings">My Bookings</a>
                        <a href="includes/logout.php">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>