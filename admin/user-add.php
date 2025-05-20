<?php

require_once('includes/user-handlers.php');

// Process user form
$formData = processUserForm();
$user = $formData['user'];
$is_edit = $formData['is_edit'];
$success_message = $formData['success_message'];
$error_message = $formData['error_message'];

$bookings = [];
if ($is_edit && isset($user['id'])) {
    $bookings = getUserBookings($conn, $user['id']);
}

$active_section = 'bookings';

?>

<?php include("includes/header.php") ?>

<h1><?php echo $is_edit ? 'Edit User: ' . htmlspecialchars($user['full_name']) : 'Add New User'; ?></h1>

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

<div class="action-buttons">
    <a href="users-view.php" class="action-btn back-btn">
        <i class="fas fa-arrow-left"></i> Back to Users
    </a>
</div>

<div class="user-form-container">
    <form method="POST" action="" class="user-form">
        <div class="form-group">
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" value="<?php echo $is_edit ? htmlspecialchars($user['full_name']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $is_edit ? htmlspecialchars($user['email']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo $is_edit ? htmlspecialchars($user['phone']) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="password"><?php echo $is_edit ? 'New Password (leave blank to keep current)' : 'Password'; ?>:</label>
            <input type="password" id="password" name="password" <?php echo $is_edit ? '' : 'required'; ?>>
        </div>

        <div class="form-group">
            <label for="position">User Role:</label>
            <select id="position" name="position" required>
                <option value="user" <?php echo $is_edit && $user['position'] == 'user' ? 'selected' : ''; ?>>User</option>
                <option value="admin" <?php echo $is_edit && $user['position'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>

        <?php if ($is_edit): ?>
            <div class="form-info">
                <p><strong>Registration Date:</strong> <?php echo date('F d, Y H:i', strtotime($user['created_at'])); ?></p>
                <p><strong>Last Login:</strong> <?php echo !empty($user['last_login']) ? date('F d, Y H:i', strtotime($user['last_login'])) : 'Never'; ?></p>
            </div>
        <?php endif; ?>

        <input type="hidden" name="user_id" value="<?php echo $is_edit ? $user['id'] : ''; ?>">
        <input type="hidden" name="action" value="<?php echo $is_edit ? 'update_user' : 'add_user'; ?>">
    </form>

    <div class="user-form-button">
        <button type="submit" class="form-buttons edit-button">
            <?php echo $is_edit ? 'Update User' : 'Create User'; ?>
        </button>
        <a href="users-view.php" class="form-buttons delete-button">Cancel</a>
    </div>
</div>

<!-- Bookings Section -->
<div class="table-responsive <?php echo $active_section === 'bookings' ? 'active' : ''; ?>">
    <h2>My Bookings</h2>

    <?php if (empty($bookings)): ?>
        <p> you have no bookings yet.</p>
    <?php else: ?>
        <table class="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Room</th>
                    <th>guests</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
            </thead>

            <?php foreach ($bookings as $booking): ?>
                <tbody>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['id']); ?></td>
                        <td><?php echo htmlspecialchars($booking['room_number'] ?? 'Room ' . $booking['room_id']); ?></td>
                        <td><?php echo htmlspecialchars($booking['guests']); ?></td>
                        <td><?php echo htmlspecialchars(date('M d, Y', strtotime($booking['check_in']))); ?></td>
                        <td><?php echo htmlspecialchars(date('M d, Y', strtotime($booking['check_out']))); ?></td>
                        <td>$<?php echo htmlspecialchars(number_format($booking['total_price'], 2)); ?></td>
                        <td><?php echo htmlspecialchars($booking['booking_status']); ?></td>
                    </tr>
                </tbody>

            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

<?php include("includes/footer.php") ?>