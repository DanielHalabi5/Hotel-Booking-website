<?php include("includes/room-handlers.php");

$success_message = $error_message = '';

handleRoomSoftDelete($conn);
?>

<?php require_once("includes/header.php") ?>

<h1 class="section_header">Room Management</h1>

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

<!-- Action Buttons -->
<div class="action-buttons">
    <a href="room-form.php" class="form-buttons nav-button">
        <i class="fas fa-plus"></i> Add New Room
    </a>
    <a href="room-form.php?type=new" class="form-buttons nav-button">
        <i class="fas fa-plus"></i> Add Room Type
    </a>
</div>
<!-- End Action Buttons -->


<!-- Filter Form -->
<div class="filter-container">
    <form action="" method="GET" class="filter-form">
        <div class="filter-group">
            <input type="text" name="search" placeholder="Search rooms..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="filter-group">
            <select name="room_type">
                <option value="">All Room Types</option>
                <?php
                $roomTypesResult->data_seek(0);
                while ($roomType = $roomTypesResult->fetch_assoc()): ?>
                    <option value="<?php echo $roomType['id']; ?>" <?php if ($room_type == $roomType['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($roomType['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="filter-group">
            <select name="status">
                <option value="">All Statuses</option>
                <option value="available" <?php if ($status == 'available') echo 'selected'; ?>>Available</option>
                <option value="occupied" <?php if ($status == 'occupied') echo 'selected'; ?>>Occupied</option>
                <option value="maintenance" <?php if ($status == 'maintenance') echo 'selected'; ?>>Maintenance</option>
            </select>
        </div>
        <div class="filter-group">
            <select name="discount">
                <option value="">All Rooms</option>
                <option value="discount" <?php if (isset($_GET['discount']) && $_GET['discount'] == 'discount') echo 'selected'; ?>>Discounted Only</option>
            </select>
        </div>
        <button type="submit" class="form-buttons filter-button">
            <i class="fas fa-filter"></i> Filter
        </button>
        <a href="rooms.php" class="form-buttons reset-filter">
            <i class="fas fa-sync-alt"></i> Reset
        </a>
    </form>
</div>
<!-- End Filter Form -->


<!-- Rooms Table -->
<div class="rooms">
    <table>
        <thead>
            <tr>
                <th>Room Number</th>
                <th>Room Type</th>
                <th>Category</th>
                <th>Capacity</th>
                <th>Base Price</th>
                <th>Discount</th>
                <th>Final Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $statusClass = 'status-' . $row['status'];

                    $hasDiscount = isset($row['discount_percentage']) && $row['discount_percentage'] > 0;
                    $basePrice = $row['price_per_night'];
                    $finalPrice = isset($row['discounted_price']) ? $row['discounted_price'] : $basePrice;
            ?>
                    <tr <?php echo $hasDiscount ? 'class="discounted-row"' : ''; ?>>
                        <td><?php echo htmlspecialchars($row['room_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['room_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><?php echo htmlspecialchars($row['capacity']); ?> guests</td>
                        <td>$<?php echo htmlspecialchars(number_format($basePrice, 2)); ?></td>
                        <td>
                            <?php if ($hasDiscount): ?>
                                <span class="discount-badge"><?php echo htmlspecialchars($row['discount_percentage']); ?>%</span>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($hasDiscount): ?>
                                <span class="discounted-price">$<?php echo htmlspecialchars(number_format($finalPrice, 2)); ?></span>
                            <?php else: ?>
                                $<?php echo htmlspecialchars(number_format($finalPrice, 2)); ?>
                            <?php endif; ?>
                        </td>
                        <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo ucfirst(htmlspecialchars($row['status'])); ?></span></td>
                        <td class="actions">
                            <a href="room-form.php?id=<?php echo $row['id']; ?>" class="table-buttons edit-button" title="Edit Room">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $row['id']; ?>)" class="table-buttons delete-button" title="Delete Room">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
            <?php
                }
            } else {
                echo '<tr><td colspan="9" class="no-records">No rooms found</td></tr>';
            }
            ?>
        </tbody>
    </table>
    <p class="roomCount">Total: <?php echo $result->num_rows; ?> rooms</p>
</div>
<!-- End Rooms Table -->


<!-- Js File -->
<script src="js/script.js?v<?= time(); ?>"></script>

<?php require_once("includes/footer.php") ?>