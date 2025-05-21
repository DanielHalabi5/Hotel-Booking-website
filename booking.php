<?php require_once('includes/header.php') ?>

<div class="booking">
    <form method="POST" action="booking.php" id="bookingForm">
        <div class="booking-container">

            <a href="index.php" class="back-button">
                <i class="fas fa-arrow-left"></i> Back To Home
            </a>

            <h2>Book Your Room</h2>
            <p>Join us and experience luxury by the ocean</p>

            <label for="roomType">Room Type</label>
            <select id="roomType" onchange="calculateTotal()">
                <option value="100">Standard - $100/night</option>
                <option value="150">Deluxe - $150/night</option>
                <option value="200">Suite - $200/night</option>
            </select>

            <label for="arriveDate">Arrival Date</label>
            <input type="date" id="arriveDate" onchange="updateNights()">

            <label for="departDate">Departure Date</label>
            <input type="date" id="departDate" onchange="updateNights()">

            <label for="numGuests">Number of Guests</label>
            <input type="number" id="numGuests" value="1" min="1">

            <label for="numNights">Number of Nights</label>
            <input type="number" id="numNights" value="1" min="1" readonly>



            <p class="total-price">Total Price: $<span id="totalPrice">0</span></p>

            <button class="submit-button" onclick="calculateTotal()">Book Now</button>
        </div>
    </form>
</div>

<?php require_once('includes/footer.php') ?>