<?php include("includes/header.php") ?>

<div class="main-content">
    <div class="signup-container">
        <div class="signup-form-wrapper">
            <h2>Create Your Account</h2>
            <p class="form-subtitle">Join Vellora and experience luxury by the ocean</p>

            <!-- Sign Up Form -->
            <form class="signup-form" action="includes/login-functions.php" method="POST">
                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                </div>

                <div class="form-row">
                    <div class="form-group half">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Create a password" required>
                    </div>

                    <div class="form-group half">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="Enter your phone number">
                </div>

                <div class="form-checkbox">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">I agree to the <a href="terms.php">Terms and Conditions</a></label>
                </div>


                <button type="submit" class="signup-button">Create Account</button>

                <div class="social-signup">
                    <p>Or sign up with</p>
                    <div class="social-buttons">
                        <a href="#" class="social-button facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                        <a href="#" class="social-button google"><i class="fa fa-google" aria-hidden="true"></i></a>
                        <a href="#" class="social-button apple"><i class="fa fa-apple" aria-hidden="true"></i></a>
                    </div>
                </div>

                <p class="login-link">Already have an account? <a href="login.php">Log In</a></p>
            </form>

        </div>
    </div>
</div>

<?php include("includes/footer.php") ?>