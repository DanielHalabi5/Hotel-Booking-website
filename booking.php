<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking Form</title>
    <style>
      body {
    font-family: Arial, sans-serif;
    background-image: url('pic.jpg');
    background-size: cover; 
    background-position: center; 
    background-repeat: no-repeat; 
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

        .booking-container {
            background: white;
            padding: 30px;
            width: 400px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            text-align: left;
        }

        h2 {
            color: #333;
            font-size: 26px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 8px;
        }

        p {
            text-align: center;
            color: #777;
            margin-bottom: 20px;
            font-size: 14px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-top: 15px;
            color: #333;
            font-size: 14px;
        }

        select, input {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            background: #f8f8f8;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            margin-top: 15px;
        }

        .checkbox-container input {
            width: auto;
            margin-right: 10px;
        }

        .checkbox-container label {
            font-weight: normal;
            font-size: 14px;
            color: #777;
        }

        .checkbox-container a {
            color: #333;
            text-decoration: none;
            font-weight: bold;
        }

        .total-price {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            text-align: center;
            margin-top: 20px;
        }

        button {
            width: 100%;
            padding: 15px;
            margin-top: 20px;
            background: #333;
            color: white;
            border: none;
            font-size: 18px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.3);
        }

        button:hover {
            background: #555;
        }
    </style>
</head>
<body>

    <div class="booking-container">
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

        <div class="checkbox-container">
            <input type="checkbox" id="terms">
            <label for="terms">I agree to the <a href="#">Terms and Conditions</a></label>
        </div>

        <p class="total-price">Total Price: $<span id="totalPrice">0</span></p>

        <button onclick="calculateTotal()">Book Now</button>
    </div>

    <script>
        function updateNights() {
            const arriveDate = new Date(document.getElementById("arriveDate").value);
            const departDate = new Date(document.getElementById("departDate").value);
            if (!isNaN(arriveDate) && !isNaN(departDate) && departDate > arriveDate) {
                const timeDiff = departDate - arriveDate;
                const nights = Math.ceil(timeDiff / (1000 * 3600 * 24));
                document.getElementById("numNights").value = nights;
            } else {
                document.getElementById("numNights").value = 1;
            }
            calculateTotal();
        }

        function calculateTotal() {
            const pricePerNight = parseInt(document.getElementById("roomType").value);
            const numNights = parseInt(document.getElementById("numNights").value);
            const totalPrice = pricePerNight * numNights;
            document.getElementById("totalPrice").textContent = totalPrice;
        }

    
        calculateTotal();
    </script>

</body>
</html>
