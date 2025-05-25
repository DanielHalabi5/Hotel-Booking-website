<?php include("includes/room-handlers.php");

if (isset($room['price_per_night']) && isset($room['discount_percentage'])) {
    if ($room['discount_percentage'] > 0) {
        $room['discounted_price'] = $room['price_per_night'] * (1 - $room['discount_percentage']/100);
    } else {
        $room['discounted_price'] = $room['price_per_night'];
    }
}

?>

<?php include("includes/header.php") ?>

<h1 class="section_header">
    <?php echo $is_room_type ? "Room Type" : "Room"; ?> <?php echo $is_edit ? "Edit" : "Add"; ?>
</h1>

<?php if (isset($success_message)): ?>
    <div class="responseMessage responseMessage__success">
        <?php echo $success_message; ?>
    </div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div class="responseMessage responseMessage__error">
        <?php echo $error_message; ?>
    </div>
<?php endif; ?>

<a href="rooms.php" class="form-buttons back-btn">
    <i class="fas fa-arrow-left"></i> Back to Rooms
</a>

<!-- If inserting a Room Type  -->
<?php if ($is_room_type): ?>
    <!-- Room Type Form -->
    <form action="" method="post" class="room-form" enctype="multipart/form-data">
        <!-- Room Type fields -->
        <div class="form-group">
            <label for="name">Room Type Name:</label>
            <input type="text" name="name" id="name" 
                   value="<?php echo isset($roomType['name']) ? htmlspecialchars($roomType['name']) : ''; ?>" 
                   required>
        </div>
        
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" id="description" rows="4" required><?php echo isset($roomType['description']) ? htmlspecialchars($roomType['description']) : ''; ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="price_per_night">Price Per Night ($):</label>
            <input type="number" step="0.01" min="0" name="price_per_night" id="price_per_night" 
                   value="<?php echo isset($roomType['price_per_night']) ? htmlspecialchars($roomType['price_per_night']) : ''; ?>" 
                   required>
        </div>
        
        <div class="form-group">
            <label for="capacity">Capacity (Guests):</label>
            <input type="number" min="1" name="capacity" id="capacity" 
                   value="<?php echo isset($roomType['capacity']) ? htmlspecialchars($roomType['capacity']) : ''; ?>" 
                   required>
        </div>
        
        <div class="form-group">
            <label for="room_size">Room Size (sq ft):</label>
            <input type="number" min="0" name="room_size" id="room_size" 
                   value="<?php echo isset($roomType['room_size']) ? htmlspecialchars($roomType['room_size']) : ''; ?>" 
                   required>
        </div>
        
        <div class="form-group">
            <label for="category">Category:</label>
            <input type="text" name="category" id="category" 
                   value="<?php echo isset($roomType['category']) ? htmlspecialchars($roomType['category']) : ''; ?>" 
                   required>
        </div>
        
        <div class="form-group">
            <label for="room_image">Room Image:</label>
            <?php if (isset($roomType['image_url']) && !empty($roomType['image_url'])): ?>
                <div class="current-image">
                    <img src="../<?php echo htmlspecialchars($roomType['image_url']); ?>" alt="Current Room Image" style="max-width: 200px;">
                    <p>Current image. Upload a new one to replace.</p>
                </div>
            <?php endif; ?>
            <input type="file" name="room_image" id="room_image" accept="image/*">
        </div>
        
        <div class="form-buttons">
            <button type="submit" name="submit_room_type" class="form-buttons submit-button">
                <i class="fas fa-save"></i> <?php echo $is_edit ? 'Update Room Type' : 'Add Room Type'; ?>
            </button>
        </div>
    </form>
    <!-- End Room Type Form -->

    <!-- If inserting a New Room -->
<?php else: ?>
    <!-- Room Form -->
    <form method="post" class="room-form">
        <div class="form-group">
            <label for="room_number">Room Number</label>
            <input type="text" id="room_number" name="room_number" required
                value="<?php echo isset($room['room_number']) ? htmlspecialchars($room['room_number']) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="room_type_id">Room Type</label>
            <select id="room_type_id" name="room_type_id" required>
                <option value="">Select Room Type</option>
                <?php
                $roomTypesResult->data_seek(0);
                while ($roomType = $roomTypesResult->fetch_assoc()): ?>
                    <option value="<?php echo $roomType['id']; ?>"
                        <?php if (isset($room['room_type_id']) && $room['room_type_id'] == $roomType['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($roomType['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                <option value="available" <?php if (isset($room['status']) && $room['status'] == 'available') echo 'selected'; ?>>Available</option>
                <option value="occupied" <?php if (isset($room['status']) && $room['status'] == 'occupied') echo 'selected'; ?>>Occupied</option>
                <option value="maintenance" <?php if (isset($room['status']) && $room['status'] == 'maintenance') echo 'selected'; ?>>Maintenance</option>
            </select>
        </div>

        <div class="form-group">
            <label for="discount_percentage">Discount (%):</label>
            <input type="number" min="0" max="100" step="0.01"
                name="discount_percentage" id="discount_percentage"
                value="<?php echo isset($room['discount_percentage']) ? $room['discount_percentage'] : '0.00'; ?>"
                class="form-control">
            <small class="form-text text-muted">Enter percentage discount for this specific room (0-100%)</small>
        </div>

        <?php if (isset($room['price_per_night']) && isset($room['discount_percentage'])): ?>
        <div class="form-group">
            <label>Base Price:</label>
            <p class="price-display">$<?php echo number_format($room['price_per_night'], 2); ?></p>
        </div>
        
        <?php if ($room['discount_percentage'] > 0): ?>
        <div class="form-group">
            <label>Discounted Price:</label>
            <p class="price-display discounted-price">
                $<?php echo number_format($room['discounted_price'], 2); ?>
                <span class="discount-badge"><?php echo $room['discount_percentage']; ?>% OFF</span>
            </p>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <button type="submit" name="submit_room" class="form-buttons submit-button">
            <i class="fas fa-save"></i>
            <?php echo $is_edit ? "Update Room" : "Add Room"; ?>
        </button>
    </form>
<?php endif; ?>
    <!-- EndRoom Form -->


<?php include("includes/footer.php") ?>