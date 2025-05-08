<?php include("includes/tables-data.php") ?>

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

<?php if ($is_room_type): ?>
    <!-- Room Type Form -->
    <form method="post" class="add-user-form" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Room Type Name</label>
            <input type="text" id="name" name="name" required
                value="<?php echo isset($roomType['name']) ? htmlspecialchars($roomType['name']) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5" required><?php echo isset($roomType['description']) ? htmlspecialchars($roomType['description']) : ''; ?></textarea>
        </div>

        <div class="form-group">
            <label for="price_per_night">Price Per Night ($)</label>
            <input type="number" step="0.01" id="price_per_night" name="price_per_night" required
                value="<?php echo isset($roomType['price_per_night']) ? htmlspecialchars($roomType['price_per_night']) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="capacity">Capacity (Max Guests)</label>
            <input type="number" id="capacity" name="capacity" required
                value="<?php echo isset($roomType['capacity']) ? htmlspecialchars($roomType['capacity']) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="room_size">Room Size</label>
            <select id="room_size" name="room_size" required>
                <option value="small" <?php if (isset($roomType['room_size']) && $roomType['room_size'] == 'small') echo 'selected'; ?>>Small</option>
                <option value="medium" <?php if (isset($roomType['room_size']) && $roomType['room_size'] == 'medium') echo 'selected'; ?>>Medium</option>
                <option value="large" <?php if (isset($roomType['room_size']) && $roomType['room_size'] == 'large') echo 'selected'; ?>>Large</option>
            </select>
        </div>

        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category" required>
                <option value="">Select Category</option>
                <option value="Standard" <?php if (isset($roomType['category']) && $roomType['category'] == 'Standard') echo 'selected'; ?>>Standard</option>
                <option value="Deluxe" <?php if (isset($roomType['category']) && $roomType['category'] == 'Deluxe') echo 'selected'; ?>>Deluxe</option>
                <option value="Suite" <?php if (isset($roomType['category']) && $roomType['category'] == 'Suite') echo 'selected'; ?>>Suite</option>
                <option value="Executive" <?php if (isset($roomType['category']) && $roomType['category'] == 'Executive') echo 'selected'; ?>>Executive</option>
                <option value="Presidential" <?php if (isset($roomType['category']) && $roomType['category'] == 'Presidential') echo 'selected'; ?>>Presidential</option>
            </select>
        </div>

        <div class="form-group">
            <label for="room_image">Room Image</label>
            <?php if (isset($roomType['image_url']) && !empty($roomType['image_url'])): ?>
                <div class="current-image">
                    <p>Current Image:</p>
                    <img src="../<?php echo htmlspecialchars($roomType['image_url']); ?>" alt="Room Image" style="max-width: 200px; margin: 10px 0;">
                </div>
            <?php endif; ?>
            <input type="file" id="room_image" name="room_image" accept="image/*" <?php echo $is_edit ? '' : 'required'; ?>>
            <p class="helper-text">Upload a high-quality image that showcases the room. Max file size: 5MB.</p>
        </div>

        <button type="submit" name="submit_room_type" class="submit-button">
            <i class="fas fa-save"></i>
            <?php echo $is_edit ? "Update Room Type" : "Add Room Type"; ?>
        </button>
    </form>

<?php else: ?>
    <!-- Room Form -->
    <form method="post" class="add-user-form">
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

        <button type="submit" name="submit_room" class="submit-button">
            <i class="fas fa-save"></i>
            <?php echo $is_edit ? "Update Room" : "Add Room"; ?>
        </button>
    </form>
<?php endif; ?>

<?php include("includes/footer.php") ?>