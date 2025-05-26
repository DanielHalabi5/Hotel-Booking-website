<?php
session_start();

$response = $_SESSION['response'] ?? null;
unset($_SESSION['response']);

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: index.php');
    exit;
}

include("includes/header.php");
?>

<div class="main-content">
    <div class="login-container">
        <div class="login-form-wrapper">
            <h2>Welcome Back</h2>
            <p class="form-subtitle">Sign in to access your Vellora account</p>

            <?php if ($response): ?>
                <div class="alert <?php echo $response['success'] ? 'alert-success' : 'alert-danger'; ?>">
                    <?php echo $response['message']; ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->

            <form class="login-form" action="includes/login-functions.php" method="post">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <div class="forgot-password">
                        <a href="forgot-password.php">Forgot password?</a>
                    </div>
                </div>

                <button type="submit" class="login-button">Log In</button>

                <div class="social-login">
                    <p>Or sign in with</p>
                    <div class="social-buttons">
                        <a href="#" class="social-button facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                        <a href="#" class="social-button google"><i class="fa fa-google" aria-hidden="true"></i></a>
                        <a href="#" class="social-button apple"><i class="fa fa-apple" aria-hidden="true"></i></a>
                    </div>
                </div>

                <p class="signup-link">Don't have an account? <a href="signup.php">Sign Up</a></p>
            </form>
        
        </div>
    </div>
</div>

<div class="footer">
    <div class="footer-bottom">
        <p>&copy; 2025 Vellora Hotel. All rights reserved.</p>
    </div>
</div>
</body>

</html>