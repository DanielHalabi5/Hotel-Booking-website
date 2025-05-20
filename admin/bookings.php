<?php

include('../includes/connection.php');
include('includes/booking-handlers.php');

$filters = initializeBookingFilters();
$search = $filters['search'];
$status = $filters['status'];
$date_from = $filters['date_from'];
$date_to = $filters['date_to'];
$sort_by = $filters['sort_by'];
$sort_order = $filters['sort_order'];

$result = getBookings($conn, $search, $status, $date_from, $date_to, $sort_by, $sort_order);
?>
<?php require_once("includes/header.php") ?>

<h1>Booking Management</h1>

<!-- Filters Section -->
<div class="booking-filters">
    <form action="" method="GET">
        <input type="text" name="search" placeholder="Search bookings..." value="<?php echo htmlspecialchars($search); ?>">

        <select name="status">
            <option value="">All Statuses</option>
            <option value="confirmed" <?php echo $status == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
            <option value="cancelled" <?php echo $status == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            <option value="completed" <?php echo $status == 'completed' ? 'selected' : ''; ?>>Completed</option>
        </select>

        <input type="date" name="date_from" placeholder="From Date" value="<?php echo htmlspecialchars($date_from); ?>">
        <input type="date" name="date_to" placeholder="To Date" value="<?php echo htmlspecialchars($date_to); ?>">

        <input type="hidden" name="sort_by" value="<?php echo htmlspecialchars($sort_by); ?>">
        <input type="hidden" name="sort_order" value="<?php echo htmlspecialchars($sort_order); ?>">

        <button class="form-buttons filter-button" type="submit">Filter</button>
        <button class="form-buttons reset-filter" type="button" onclick="window.location.href='bookings.php'">Reset</button>
    </form>
</div>
<!-- End Filters Section -->

<!-- Bookings Table -->
<div class="table-responsive">
    <table class="booking-table">
        <thead>
            <tr>
                <th onclick="sortTable('id')">Booking ID</th>
                <th onclick="sortTable('guest_name')">Guest</th>
                <th onclick="sortTable('room_number')">Room</th>
                <th onclick="sortTable('check_in')">Check-in</th>
                <th onclick="sortTable('check_out')">Check-out</th>
                <th onclick="sortTable('total_price')">Total Price</th>
                <th onclick="sortTable('booking_status')">Status</th>
                <th onclick="sortTable('booking_date')">Booking Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $status_class = 'status-' . $row['booking_status'];

                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['guest_name']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['room_number']) . '   (' . htmlspecialchars($row['room_type']) . ')</td>';
                    echo '<td>' . date('M d, Y', strtotime($row['check_in'])) . '</td>';
                    echo '<td>' . date('M d, Y', strtotime($row['check_out'])) . '</td>';
                    echo '<td>$' . number_format($row['total_price'], 2);
                    if (isset($row['discount_percentage']) && $row['discount_percentage'] > 0) {
                        echo ' <span class="discount-indicator">' . htmlspecialchars($row['discount_percentage']) . '% OFF</span>';
                    }
                    echo '</td>';
                    echo '<td>' . htmlspecialchars($row['booking_status']) . '</td>';
                    echo '<td>' . date('M d, Y', strtotime($row['booking_date'])) . '</td>';
                    echo '<td class="action-buttons">';
                    echo '<a href="booking-form.php?id=' . $row['id'] . '" class="form-buttons view-btn">Details</a>';
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="9">No bookings found</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>
<!-- End Bookings Table -->

<!-- Js File -->
<script src="js/script.js?v<?= time(); ?>"></script>

<?php require_once("includes/footer.php") ?>