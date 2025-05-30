<?php

require_once 'includes/connection.php';

$sql = "
    SELECT rt.*,
           COUNT(r.id) as total_rooms,
           COUNT(CASE WHEN r.status = 'available' THEN 1 END) as available_rooms,
           AVG(r.discount_percentage) as avg_discount_percentage
    FROM room_types rt 
    LEFT JOIN rooms r ON rt.id = r.room_type_id AND r.deleted_at IS NULL
    GROUP BY rt.id, rt.category, rt.name, rt.description, rt.price_per_night, rt.image_url, rt.capacity, rt.room_size
    ORDER BY rt.category, rt.price_per_night
";

$result = $conn->query($sql);

$roomsByCategory = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $category = $row['category'];
        if (!isset($roomsByCategory[$category])) {
            $roomsByCategory[$category] = [];
        }
        $roomsByCategory[$category][] = $row;
    }
}

include('includes/header.php') ?>

<!-- Sign up -->

<?php if (!$logged_in): ?>
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

<?php else: ?>
    <div class="banner">
        <div class="BannerHeader">
            <h1>Vellora Hotel</h1>
            <p>Experience Luxury & Comfort</p>
            <p class="BannerTagline">Where unforgettable memories await in an oasis of elegance and tranquility</p>
            <div class="signup-button">
                <a href="booking.php">Book Now</a>
            </div>
        </div>
    </div>

    <!-- Room Showcase -->
    <section class="rooms-section">
        <div class="container">
            <h2 class="section-title">Our Luxurious Accommodations</h2>

            <div class="room-cards">
                <?php
                $roomCount = 0;

                foreach ($roomsByCategory as $category => $rooms) {
                    if ($roomCount >= 3) break;

                    if (!empty($rooms)) {
                        $room = $rooms[0];
                        
                        $originalPrice = $room['price_per_night'];
                        $discountedPrice = $originalPrice;
                        $discountPercentage = isset($room['discount_percentage']) ? $room['discount_percentage'] : 0;
                        
                        if ($discountPercentage > 0) {
                            $discountedPrice = $originalPrice * (1 - $discountPercentage / 100);
                        }
                ?>
                        <div class="room-card">
                            <div class="room-image">
                                <img src="<?php echo htmlspecialchars($room['image_url']); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>">
                                
                                <?php if ($room['available_rooms'] > 0): ?>
                                    <div class="availability-badge available">
                                        <?php echo $room['available_rooms']; ?> Available
                                    </div>
                                <?php elseif ($room['total_rooms'] > 0): ?>
                                    <div class="availability-badge unavailable">
                                        Fully Booked
                                    </div>
                                <?php else: ?>
                                    <div class="availability-badge coming-soon">
                                        Coming Soon
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="room-info">
                                <h6 class="section-title"><?php echo htmlspecialchars($category); ?></h6>
                                <h3><?php echo htmlspecialchars($room['name']); ?></h3>
                                <p><?php echo htmlspecialchars($room['description']); ?></p>

                                <?php if ($room['available_rooms'] > 0): ?>
                                    <p class="availability-status available">
                                        <i class="fas fa-check-circle"></i>
                                        <?php echo $room['available_rooms']; ?> room<?php echo $room['available_rooms'] > 1 ? 's' : ''; ?> available
                                    </p>
                                <?php elseif ($room['total_rooms'] > 0): ?>
                                    <p class="availability-status unavailable">
                                        <i class="fas fa-times-circle"></i>
                                        Currently fully booked
                                    </p>
                                <?php else: ?>
                                    <p class="availability-status no-rooms">
                                        <i class="fas fa-clock"></i>
                                        Rooms under preparation
                                    </p>
                                <?php endif; ?>

                                <?php if ($discountPercentage > 0): ?>
                                    <div class="price-section">
                                        <span class="discount-badge"><?php echo $discountPercentage; ?>% OFF</span>
                                        <p class="room-price">
                                            <span class="original-price">$<?php echo number_format($originalPrice, 2); ?></span>
                                            <span class="discounted-price">$<?php echo number_format($discountedPrice, 2); ?>/night</span>
                                        </p>
                                    </div>
                                <?php else: ?>
                                    <p class="room-price">From $<?php echo number_format($originalPrice, 2); ?>/night</p>
                                <?php endif; ?>
                                <div class="room-actions">
                                    <?php if ($room['available_rooms'] > 0): ?>
                                        <a href="room-details.php?id=<?php echo $room['id']; ?>" class="room-button">View Details</a>
                                        <a href="booking.php?room_id=<?php echo $room['id']; ?>" class="room-button book-now">Book Now</a>
                                    <?php elseif ($room['total_rooms'] > 0): ?>
                                        <a href="room-details.php?id=<?php echo $room['id']; ?>" class="room-button">View Details</a>
                                        <a href="waitlist.php?room_id=<?php echo $room['id']; ?>" class="room-button secondary">Join Waitlist</a>
                                    <?php else: ?>
                                        <a href="room-details.php?id=<?php echo $room['id']; ?>" class="room-button">View Details</a>
                                        <button class="room-button disabled" disabled>Coming Soon</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                <?php
                        $roomCount++;
                    }
                }
                ?>
            </div>
            <div class="view-all">
                <a href="rooms.php" class="view-all-button">View All Rooms</a>
            </div>
        </div>
    </section>

<?php endif; ?>

<!-- About Us Section -->
<section class="about-section" id="about-section">
    <div class="container">
        <h2 class="section-title">About Us</h2>
        <div class="about-content">
            <div class="about-text">
                <h2 class="h2-style"> Welcome to Vellora </h2>
                <p>Nestled in the heart of the city, Vellora Hotel offers a perfect blend of luxury, comfort, and exceptional service. Since our establishment in 2005, we have been dedicated to providing our guests with an unforgettable experience.</p>
                <p>Our award-winning facilities and attentive staff ensure that your stay with us is nothing short of extraordinary.</p>
            </div>
            <div class="about-image">
                <img src="image/hotelFront.jpg" alt="Vellora Hotel Exterior">
            </div>
        </div>
    </div>
</section>
<!-- End About Us Section -->

<!-- Contact us -->
<div id="contact-section" class="contact-section">
    <h2 class="h2-style">Contact Us</h2>
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
            <form action="contact.php" method="post">
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