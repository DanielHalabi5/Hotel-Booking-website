<?php

include('../includes/connection.php');
include('includes/tables-data.php');

$bookingData = processBookingForm();

if (!$bookingData) {
    header('Location: bookings.php');
    exit;
}

$booking = $bookingData['booking'];
$duration = $bookingData['duration'];
$success_message = $bookingData['success_message'];
$error_message = $bookingData['error_message'];

?>

<!-- Booking Form  -->

<?php require_once("includes/header.php") ?>

<!-- Js File -->
<script src="js/script.js?v<?= time(); ?>"></script>

<h1>Booking Details</h1>

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

<!-- Booking Form Buttons -->
<div class="booking-form-buttons">
    <a href="bookings.php" class="booking-form-buttons back-btn ">
        <i class="fas fa-arrow-left"></i> Back to Bookings
    </a>
    <button class="booking-form-buttons email-btn">
        <i class="fas fa-envelope"></i> Email to Guest
    </button>
    <?php if ($booking['booking_status'] != 'cancelled'): ?>
        <button class="booking-form-buttons cancel-btn" onclick="cancelBooking(<?php echo $booking['id']; ?>)">
            Cancel Booking
        </button>
    <?php endif; ?>
</div>
<!-- End Booking Form Buttons -->

<div class="booking-details-container">
    <div class="booking-main-info">
        <!-- Booking Information -->
        <div class="booking-info">
            <h2>Booking Information</h2>
            <div class="info-grid">
                <div class="info-label">Booking Reference:</div>
                <div class="info-value"><?php echo htmlspecialchars($booking['id']); ?></div>

                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span class="booking-status-badge status-<?php echo $booking['booking_status']; ?>">
                        <?php echo ucfirst($booking['booking_status']); ?>
                    </span>
                </div>

                <div class="info-label">Check-in Date:</div>
                <div class="info-value"><?php echo date('F d, Y', strtotime($booking['check_in'])); ?></div>

                <div class="info-label">Check-out Date:</div>
                <div class="info-value"><?php echo date('F d, Y', strtotime($booking['check_out'])); ?></div>

                <div class="info-label">Duration:</div>
                <div class="info-value"><?php echo $duration; ?> nights</div>

                <div class="info-label">Booking Date:</div>
                <div class="info-value"><?php echo date('F d, Y H:i', strtotime($booking['booking_date'])); ?></div>

                <div class="info-label">Guests:</div>
                <div class="info-value"><?php echo $booking['guests']; ?></div>


            </div>

            <form action="" method="POST" class="status-form">
                <h3>Update Booking Status</h3>
                <select name="booking_status">
                    <option value="pending" <?php echo $booking['booking_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="confirmed" <?php echo $booking['booking_status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="cancelled" <?php echo $booking['booking_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    <option value="completed" <?php echo $booking['booking_status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                </select>
                <button type="submit" name="update_status" class="submit-btn">Update Status</button>
            </form>
        </div>
        <!-- End Booking Information -->

        <!-- Guest Information -->
        <div class="guest-info">
            <h2>Guest Information</h2>
            <div class="info-grid">
                <div class="info-label">Guest Name:</div>
                <div class="info-value"><?php echo htmlspecialchars($booking['guest_name']); ?></div>

                <div class="info-label">Email:</div>
                <div class="info-value"><?php echo htmlspecialchars($booking['guest_email']); ?></div>

                <div class="info-label">Phone:</div>
                <div class="info-value"><?php echo htmlspecialchars($booking['guest_phone']); ?></div>
            </div>
        </div>
        <!-- End Guest Information -->

        <!-- Room Information -->
        <div class="room-info">
            <h2>Room Information</h2>
            <div class="info-grid">
                <div class="info-label">Room Number:</div>
                <div class="info-value"><?php echo htmlspecialchars($booking['room_number']); ?></div>

                <div class="info-label">Room Type:</div>
                <div class="info-value"><?php echo htmlspecialchars($booking['room_type']); ?></div>

                <div class="info-label">Category:</div>
                <div class="info-value"><?php echo htmlspecialchars($booking['category']); ?></div>

                <div class="info-label">Capacity:</div>
                <div class="info-value"><?php echo $booking['capacity']; ?> person(s)</div>

                <div class="info-label">Room Size:</div>
                <div class="info-value"><?php echo $booking['room_size']; ?> sq ft</div>

                <div class="info-label">Description:</div>
                <div class="info-value"><?php echo htmlspecialchars($booking['room_description']); ?></div>
            </div>
        </div>
        <!-- End Room Information -->
    </div>

    <div class="booking-side-info">
        <!-- Payment Information -->
        <div class="payment-info">
            <h2>Payment Information</h2>
            <div class="info-grid">
                <div class="info-label">Price per Night:</div>
                <div class="info-value">$<?php echo number_format($booking['price_per_night'], 2); ?></div>

                <div class="info-label">Number of Nights:</div>
                <div class="info-value"><?php echo $duration; ?></div>

                <div class="info-label">Subtotal:</div>
                <div class="info-value">$<?php echo number_format($booking['price_per_night'] * $duration, 2); ?></div>

                <?php if (!empty($booking['additional_services'])): ?>
                    <div class="info-label">Additional Services:</div>
                    <div class="info-value">$<?php echo number_format($booking['additional_services'], 2); ?></div>
                <?php endif; ?>

                <div class="info-label">Total Price:</div>
                <div class="info-value"><strong>$<?php echo number_format($booking['total_price'], 2); ?></strong></div>

                <div class="info-label">Payment Status:</div>
                <div class="info-value">Not Payed Yet</div>
            </div>
        </div>
        <!-- End Payment Information -->
    </div>
</div>


<?php require_once("includes/footer.php") ?>