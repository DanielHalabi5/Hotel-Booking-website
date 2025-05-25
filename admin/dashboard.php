<?php include("includes/dashboard-handlers.php")?>

<?php require_once("includes/header.php") ?>

<h1>Dashboard</h1>

<!-- Statistics Section -->
<div class="statistics-container">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-details">
            <h3>Total Bookings</h3>
            <p class="stat-number"><?php echo $totalBookings; ?></p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-bed"></i>
        </div>
        <div class="stat-details">
            <h3>Current Occupancy</h3>
            <p class="stat-number"><?php echo $occupancyRate; ?>%</p>
            <p class="stat-subtext"><?php echo $occupiedRooms; ?> of <?php echo $totalRooms; ?> rooms</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-details">
            <h3>Monthly Revenue</h3>
            <p class="stat-number">$<?php echo number_format($monthlyRevenue, 2); ?></p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-details">
            <h3>Pending Bookings</h3>
            <p class="stat-number"><?php echo $pendingBookings; ?></p>
        </div>
    </div>
</div>
<!-- End Statistics Section -->

<!-- Recent Activity Section -->
<h2>Recent Activity</h2>
<div class="table-responsive">
    <table class="users-bookings-table">
        <thead>
            <tr>
                <th>Activity</th>
                <th>Details</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($recentResult->num_rows > 0) {
                while ($row = $recentResult->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>New Booking</td>';
                    echo '<td>' . htmlspecialchars($row['guest_name']) . ' - Check-in: ' . htmlspecialchars($row['check_in']) . '</td>';
                    echo '<td>' . date('M d, Y', strtotime($row['booking_date'])) . '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="3">No recent activity</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>
<!-- End Recent Activity Section -->


<?php require_once("includes/footer.php") ?>