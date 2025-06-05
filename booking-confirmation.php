<?php
require_once 'includes/connection.php';
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$booking_id = $_GET['id'];

$sql = "SELECT b.*, r.room_number, rt.name as room_name, rt.price_per_night, u.email 
        FROM bookings b
        JOIN rooms r ON b.room_id = r.id
        JOIN room_types rt ON r.room_type_id = rt.id
        LEFT JOIN users u ON b.user_id = u.id
        WHERE b.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: home.php');
    exit;
}

$booking = $result->fetch_assoc();

$check_in = new DateTime($booking['check_in']);
$check_out = new DateTime($booking['check_out']);
$nights = $check_out->diff($check_in)->days;
?>

<?php include('includes/header.php'); ?>

<!-- Main Content -->
<div class="main-content">
    <section class="confirmation-section">
        <div class="container">
            <h2 class="section-title">Booking Confirmation</h2>
            <div class="confirmation-box">
                <div class="confirmation-header">
                    <i class="fas fa-check-circle"></i>
                    <h3>Thank You for Your Reservation!</h3>
                    <p>Your booking has been confirmed. We're looking forward to welcoming you at Vellora Hotel.</p>
                </div>

                <div class="booking-details">
                    <h4>Booking Details</h4>
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Booking ID:</span>
                            <span class="detail-value"><?php echo $booking['id']; ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Room Type:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($booking['room_name']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Room Number:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($booking['room_number']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Check-in:</span>
                            <span class="detail-value"><?php echo date('F j, Y', strtotime($booking['check_in'])); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Check-out:</span>
                            <span class="detail-value"><?php echo date('F j, Y', strtotime($booking['check_out'])); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Guests:</span>
                            <span class="detail-value"><?php echo $booking['guests']; ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Nights:</span>
                            <span class="detail-value"><?php echo $nights; ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Total Price:</span>
                            <span class="detail-value">$<?php echo number_format($booking['total_price'], 2); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Status:</span>
                            <span class="detail-value status-<?php echo strtolower($booking['booking_status']); ?>">
                                <?php echo ucfirst($booking['booking_status']); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="confirmation-buttons">
                    <a href="index.php" class="back-button">Return to Home</a>
                    <p class="booking-note"> Save or Screen Shot This Page </p>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include('includes/footer.php') ?>


</body>

</html>