<?php
include('includes/connection.php');


if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $password, $email);
    $stmt->execute();

    $success_message = "Password updated. <a href='login.php'> Login   <i class='fas fa-arrow-right'></i></a> ";
} else {
    $error_message =  "Error: Missing data.";
}


include('includes/header.php');
?>



<div class="forgot-password-container">

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <h2>Forgot Password?</h2>

    <form method="post" action="forgot-password.php" class="forgot-password-form">
        <label>Email:</label>
        <input type="email" name="email" required>

        <label>New Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Reset Password</button>
    </form>
</div>

<?php include('includes/footer.php'); ?>