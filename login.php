<?php include("includes/login-auth.php")?>

<?php include("includes/header.php")?>
    <div class="main-content">
        <div class="login-container">
            <div class="login-form-wrapper">
                <h2>Welcome Back</h2>
                <p class="form-subtitle">Sign in to access your Vellora account</p>                
                <form class="login-form" action="includes/login-auth.php" method="post">
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

<?php include("includes/footer.php")?>