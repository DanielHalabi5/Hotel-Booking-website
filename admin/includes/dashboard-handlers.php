    <?php

    include('../includes/connection.php');
    include("admin-handlers.php");

    // Total bookings
    $bookingsQuery = "SELECT COUNT(*) as total_bookings FROM bookings";
    $bookingsResult = $conn->query($bookingsQuery);
    $totalBookings = $bookingsResult->fetch_assoc()['total_bookings'];

    // Current occupancy
    $todayDate = date('Y-m-d');
    $occupancyQuery = "SELECT COUNT(*) as occupied_rooms FROM bookings 
                    WHERE '$todayDate' BETWEEN check_in AND check_out 
                    AND booking_status = 'confirmed'";
    $occupancyResult = $conn->query($occupancyQuery);
    $occupiedRooms = $occupancyResult->fetch_assoc()['occupied_rooms'];

    // Total rooms
    $roomsQuery = "SELECT COUNT(*) as total_rooms FROM rooms";
    $roomsResult = $conn->query($roomsQuery);
    $totalRooms = $roomsResult->fetch_assoc()['total_rooms'];

    // Calculate occupancy rate
    $occupancyRate = ($totalRooms > 0) ? round(($occupiedRooms / $totalRooms) * 100, 2) : 0;

    // Monthly revenue
    $monthStart = date('Y-m-01');
    $monthEnd = date('Y-m-t');
    $revenueQuery = "SELECT SUM(total_price) as monthly_revenue FROM bookings 
                    WHERE check_in BETWEEN '$monthStart' AND '$monthEnd' 
                    AND booking_status = 'confirmed'";
    $revenueResult = $conn->query($revenueQuery);
    $monthlyRevenue = $revenueResult->fetch_assoc()['monthly_revenue'] ?: 0;

    // Pending bookings
    $pendingQuery = "SELECT COUNT(*) as pending_bookings FROM bookings 
                    WHERE booking_status = 'pending'";
    $pendingResult = $conn->query($pendingQuery);
    $pendingBookings = $pendingResult->fetch_assoc()['pending_bookings'];

    // Most popular room
    $popularRoomQuery = "SELECT rt.name, COUNT(b.id) as booking_count 
                        FROM bookings b 
                        JOIN rooms r ON b.room_id = r.id 
                        JOIN room_types rt ON r.room_type_id = rt.id 
                        GROUP BY rt.id 
                        ORDER BY booking_count DESC 
                        LIMIT 1";
    $popularRoomResult = $conn->query($popularRoomQuery);
    $popularRoom = $popularRoomResult->fetch_assoc();
    $mostPopularRoom = $popularRoom ? $popularRoom['name'] : 'N/A';

    // Room availability statistics
    $availableRoomsQuery = "SELECT COUNT(*) as available_rooms FROM rooms WHERE status = 'available'";
    $availableRoomsResult = $conn->query($availableRoomsQuery);
    $availableRooms = $availableRoomsResult->fetch_assoc()['available_rooms'];

    $maintenanceRoomsQuery = "SELECT COUNT(*) as maintenance_rooms FROM rooms WHERE status = 'maintenance'";
    $maintenanceRoomsResult = $conn->query($maintenanceRoomsQuery);
    $maintenanceRooms = $maintenanceRoomsResult->fetch_assoc()['maintenance_rooms'];

    // Revenue by room type
    $roomTypeRevenueQuery = "SELECT rt.name, SUM(b.total_price) as revenue 
                            FROM bookings b 
                            JOIN rooms r ON b.room_id = r.id 
                            JOIN room_types rt ON r.room_type_id = rt.id 
                            WHERE b.booking_status = 'confirmed' 
                            AND b.check_in BETWEEN '$monthStart' AND '$monthEnd'
                            GROUP BY rt.id 
                            ORDER BY revenue DESC";
    $roomTypeRevenueResult = $conn->query($roomTypeRevenueQuery);

    // Average length of stay
    $stayLengthQuery = "SELECT AVG(DATEDIFF(check_out, check_in)) as avg_stay_length 
                    FROM bookings 
                    WHERE booking_status = 'confirmed'";
    $stayLengthResult = $conn->query($stayLengthQuery);
    $avgStayLength = round($stayLengthResult->fetch_assoc()['avg_stay_length'], 1);

    // Fetch recent bookings
    $recentQuery = "SELECT b.id, u.full_name as guest_name,
        b.check_in, b.check_out, b.booking_status, b.booking_date
        FROM bookings b
        LEFT JOIN users u ON b.user_id = u.id
        ORDER BY b.booking_date DESC
        LIMIT 5";
    $recentResult = $conn->query($recentQuery);

    ?>
    