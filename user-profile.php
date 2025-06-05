	<?php
include('includes/user-information.php');
include('includes/header.php') ?>


<a href="index.php" class="back-button">
    <i class="fas fa-arrow-left"></i> Back To Home
</a>

<div class="user-info-container">
    <h1 class="section-title">My Account</h1>

    <div class="tabs">
        <div class="tab <?php echo $active_section === 'profile' ? 'active' : ''; ?>"
            onclick="location.href='?section=profile'">Profile</div>
        <div class="tab <?php echo $active_section === 'bookings' ? 'active' : ''; ?>"
            onclick="location.href='?section=bookings'">Bookings</div>
    </div>

    <!-- Profile Section -->
    <div class="content <?php echo $active_section === 'profile' ? 'active' : ''; ?>">
        <h2>Profile</h2>

        <?php if ($update_success): ?>
            <div class="message success">Profile updated successfully.</div>
        <?php endif; ?>

        <?php if (!empty($update_error)): ?>
            <div class="message error"><?php echo htmlspecialchars($update_error); ?></div>
        <?php endif; ?>



        <form method="post" class="user-info-form" id="user-info">
            <div class="first-section">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($full_name); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>" required>
                </div>
            </div>

            <h3>Change Password</h3>
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password">
            </div>

            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password">

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password">
                </div>
            </div>

            <button type="submit" name="update_profile" class="back-button">Update Profile</button>
        </form>
    </div>

    <!-- Bookings Section -->
    <div class="content <?php echo $active_section === 'bookings' ? 'active' : ''; ?>" id="user-bookings">
        <h2>My Bookings</h2>

        <?php if (empty($bookings)): ?>
            <p>You have no bookings yet.</p>
        <?php else: ?>
            <table class="user-info-table">
                <tr>
                    <th>ID</th>
                    <th>Room</th>
                    <th>guests</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['id']); ?></td>
                        <td><?php echo htmlspecialchars($booking['room_number'] ?? 'Room ' . $booking['room_id']); ?></td>
                        <td><?php echo htmlspecialchars($booking['guests']); ?></td>
                        <td><?php echo htmlspecialchars(date('M d, Y', strtotime($booking['check_in']))); ?></td>
                        <td><?php echo htmlspecialchars(date('M d, Y', strtotime($booking['check_out']))); ?></td>
                        <td>$<?php echo htmlspecialchars(number_format($booking['total_price'], 2)); ?></td>
                        <td><?php echo htmlspecialchars($booking['booking_status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</div>
<?php include("includes/footer.php") ?>