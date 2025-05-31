<?php
require_once('includes/header.php');
require_once('includes/booking-functions.php');
?>

<div class="booking">

    <form method="POST" action="booking.php" id="bookingForm">
        <div class="booking-container">

            <a href="index.php" class="back-button">
                <i class="fas fa-arrow-left"></i> Back To Home
            </a>

            <?php if ($booking_result): ?>
                <?php if (!$booking_result['success']): ?>
                    <div class="message error">
                        <h3>Booking Error</h3>
                        <p><?php echo $booking_result['message']; ?></p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <h2>Book Your Room</h2>
            <p>Join us and experience luxury by the ocean</p>

            <label for="room_id">Select Room Type:</label>
            <select name="room_id" id="room_id" required>
                <option value="">-- Select a Room --</option>
                <?php if (isset($room_types)): ?>
                    <?php foreach ($room_types as $room): ?>
                        <option value="<?php echo $room['id']; ?>"
                            data-price="<?php echo $room['price_per_night']; ?>"
                            data-capacity="<?php echo $room['capacity']; ?>"
                            <?php echo (isset($selected_room_id) && $selected_room_id == $room['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($room['name']); ?> - $<?php echo $room['price_per_night']; ?>/night
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <label for="arriveDate">Arrival Date</label>
            <input type="date" name="arriveDate" id="arriveDate" required min="<?php echo date('Y-m-d'); ?>">

            <label for="departDate">Departure Date</label>
            <input type="date" name="departDate" id="departDate" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">

            <label for="numGuests">Number of Guests</label>
            <select name="guests" id="numGuests" required>
                <option value="">-- Select Number of Guests --</option>
            </select>

            <label for="numNights">Number of Nights</label>
            <input type="number" id="numNights" value="1" min="1" readonly>

            <p class="total-price">Total Price: $<span id="totalPrice">0</span></p>

            <button type="submit" class="submit-button">Book Now</button>
        </div>
    </form>
</div>

<?php require_once('includes/footer.php') ?>