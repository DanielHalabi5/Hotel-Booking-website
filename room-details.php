<?php
require_once 'includes/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: rooms.php');
    exit;
}

$room_id = $_GET['id'];

$sql = "
    SELECT rt.*,
           COUNT(r.id) as total_rooms,
           COUNT(CASE WHEN r.status = 'available' THEN 1 END) as available_rooms,
           AVG(r.discount_percentage) as avg_discount_percentage
    FROM room_types rt 
    LEFT JOIN rooms r ON rt.id = r.room_type_id AND r.deleted_at IS NULL
    WHERE rt.id = ?
    GROUP BY rt.id, rt.category, rt.name, rt.description, rt.price_per_night, rt.image_url, rt.capacity, rt.room_size
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: rooms.php');
    exit;
}

$room = $result->fetch_assoc();

$originalPrice = $room['price_per_night'];
$discountedPrice = $originalPrice;
$discountPercentage = isset($room['avg_discount_percentage']) ? $room['avg_discount_percentage'] : 0;

if ($discountPercentage > 0) {
    $discountedPrice = $originalPrice * (1 - $discountPercentage / 100);
}

include("includes/header.php") ?>

<!-- Room Details Content -->

<div class="main-content">
    <section class="room-details-section">
        <div class="container">

            <!-- Back Button -->
             
            <a href="rooms.php" class="back-button">
                <i class="fas fa-arrow-left"></i> Back To Home
            </a>

            <h2 class="section-title"><?php echo htmlspecialchars($room['name']); ?></h2>

            <div class="room-details-container">
                <div class="room-gallery">
                    <div class="main-image">
                        <img src="<?php echo htmlspecialchars($room['image_url']) ?>" alt="<?php echo htmlspecialchars($room['name']); ?>">

                        <?php if ($room['available_rooms'] > 0): ?>
                            <div class="availability">
                                <?php echo $room['available_rooms']; ?> Available
                            </div>
                        <?php elseif ($room['total_rooms'] > 0): ?>
                            <div class="availability">
                                Fully Booked
                            </div>
                        <?php else: ?>
                            <div class="availability">
                                Coming Soon
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="room-info-container">
                    <div class="room-description">
                        <h3>Description</h3>
                        <p><?php echo htmlspecialchars($room['description']); ?></p>
                    </div>

                    <div class="availability-section">
                        <h3>Availability Status</h3>
                        <?php if ($room['available_rooms'] > 0): ?>
                            <p>
                                <i class="fas fa-check-circle"></i>
                                <strong><?php echo $room['available_rooms']; ?> room<?php echo $room['available_rooms'] > 1 ? 's' : ''; ?> available</strong> out of <?php echo $room['total_rooms']; ?> total rooms
                            </p>
                        <?php elseif ($room['total_rooms'] > 0): ?>
                            <p>
                                <i class="fas fa-times-circle"></i>
                                <strong>Currently fully booked</strong> - All <?php echo $room['total_rooms']; ?> rooms are occupied
                            </p>
                        <?php else: ?>
                            <p>
                                <i class="fas fa-clock"></i>
                                <strong>Rooms under preparation</strong> - This room type will be available soon
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="room-features">
                        <h3>Room Features</h3>
                        <ul>
                            <li><i class="fas fa-users"></i> Capacity: <?php echo $room['capacity']; ?> Person(s)</li>
                            <li><i class="fas fa-expand-arrows-alt"></i> Room Size: <?php echo htmlspecialchars($room['room_size']); ?></li>
                        </ul>
                    </div>

                    <div>
                        <h3>Pricing</h3>

                        <?php if ($discountPercentage > 0): ?>
                            <div class="discount-section">
                                <span class="discount-badge">Discount : <?php echo number_format($discountPercentage, 0); ?>% OFF</span>
                                <span>Original Price : $<?php echo number_format($originalPrice, 2); ?></span>
                                <span>Total Price : $<?php echo number_format($discountedPrice, 2); ?></span>
                                <span>per night</span>
                            </div>
                        <?php else: ?>
                            <p class="">$<?php echo number_format($originalPrice, 2); ?> <span>per night</span></p>
                        <?php endif; ?>

                        <div class="booking-actions">
                            <?php if ($room['available_rooms'] > 0): ?>
                                <a href="booking.php?room_id=<?php echo $room['id']; ?>" class="book-now-button">Book Now</a>
                                <p class="booking-note">
                                    <i class="fas fa-info-circle"></i>
                                    Immediate booking available
                                </p>
                            <?php else: ?>
                                <button class="coming-soon-button" disabled>Coming Soon</button>
                                <p class="booking-note">
                                    <i class="fas fa-hammer"></i>
                                    This room type is currently being prepared
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>