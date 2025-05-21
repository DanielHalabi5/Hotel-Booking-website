<?php include('includes/header.php') ?>
<div class="main-content">
    <div class="nav">
        <div class="BannerHeader">
            <h1>Vellora</h1>
            <p>Where Ocean Meets Luxury</p>
        </div>
        <p class="BannerTagline">Escape to a haven of coastal elegance, where breathtaking views, refined comfort, and personalized hospitality await. Unwind, explore, and indulge in serenity.
            Your seaside sanctuary awaits.
        </p>
        <div class="signup-button">
            <a href="signup.php">Sign Up Now</a>
        </div>
    </div>
</div>

<!-- Contact Section -->
<div id="contact-section" class="contact-section">
    <h2>Contact Us</h2>
    <div class="contact-container">
        <div class="contact-info">
            <h3>Get in Touch</h3>
            <p><i class="fa fa-map-marker" aria-hidden="true"></i> 123 Ocean Drive, Seaside, CA 95050</p>
            <p><i class="fa fa-phone" aria-hidden="true"></i> +1 (555) 123-4567</p>
            <p><i class="fa fa-envelope" aria-hidden="true"></i> info@vellorahotel.com</p>
            <div class="social-icons">
                <a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                <a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a>
                <a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a>
                <a href="#"><i class="fa fa-linkedin" aria-hidden="true"></i></a>
            </div>
        </div>

        <div class="contact-form">
            <h3>Send Us a Message</h3>
            <form action="contactus.php" method="post">
                <div class="form-group">
                    <input type="text" name="name" placeholder="Your Name" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Your Email" required>
                </div>
                <div class="form-group">
                    <input type="text" name="subject" placeholder="Subject">
                </div>
                <div class="form-group">
                    <textarea name="message" placeholder="Your Message" required></textarea>
                </div>
                <button type="submit" class="submit-btn">Send Message</button>
            </form>
        </div>
    </div>
</div>


<?php require_once 'includes/footer.php'; ?>