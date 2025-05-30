<?php

require_once 'includes/connection.php';

$sql = "
    SELECT rt.*,
           COUNT(r.id) as total_rooms,
           COUNT(CASE WHEN r.status = 'available' THEN 1 END) as available_rooms,
           AVG(r.discount_percentage) as avg_discount_percentage
    FROM room_types rt 
    LEFT JOIN rooms r ON rt.id = r.room_type_id AND r.deleted_at IS NULL
    GROUP BY rt.id, rt.category, rt.name, rt.description, rt.price_per_night, rt.image_url, rt.capacity, rt.room_size
    ORDER BY rt.category, rt.price_per_night
";

$result = $conn->query($sql);

$roomsByCategory = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $category = $row['category'];
        if (!isset($roomsByCategory[$category])) {
            $roomsByCategory[$category] = [];
        }
        $roomsByCategory[$category][] = $row;
    }
}

require_once 'includes/header.php'; ?>

<!-- Main Content -->
<div class="search-filter-container" role="search" aria-label="Room search">
    <div class="search-area">
        <label for="roomSearch">Search rooms</label>
        <input type="text" id="roomSearch" placeholder="Search rooms..." aria-describedby="searchHint">
        <button type="button" id="searchBtn" aria-label="Search">
            <i class="fas fa-search" aria-hidden="true"></i>
        </button>
    </div>
    <div class="filter-area">
        <label for="roomType" class="filter-label">Room Type:</label>
        <select id="roomType" aria-label="Filter by room type">
            <option value="all">All Room Types</option>
            <option value="small">Small Sized Rooms</option>
            <option value="medium">Medium Sized Rooms</option>
            <option value="large">Large Sized Rooms</option>
            <option value="theme">Theme Based Rooms</option>
        </select>
        <label for="priceRange" class="filter-label">Price Range:</label>
        <select id="priceRange" aria-label="Filter by price range">
            <option value="all">All Price Ranges</option>
            <option value="0-150">$0-$150</option>
            <option value="151-300">$151-$300</option>
            <option value="301-500">$301-$500</option>
        </select>
        <label for="availabilityFilter" class="filter-label">Availability:</label>
        <select id="availabilityFilter" aria-label="Filter by availability">
            <option value="all">All Rooms</option>
            <option value="available">Available Only</option>
            <option value="unavailable">Fully Booked</option>
            <option value="coming-soon">Coming Soon</option>
        </select>
        <button type="button" id="filterBtn">Apply Filters</button>
    </div>
    <div id="searchResults" class="search-results-info" aria-live="polite"></div>
</div>

<section class="rooms-section">
    <div class="container">
        <h2 class="section-title">Our Luxurious Accommodations</h2>

        <?php foreach ($roomsByCategory as $category => $rooms): ?>
            <h6 class="section-title"><?php echo htmlspecialchars($category); ?>:</h6>
            <div class="room-cards">
                <?php foreach ($rooms as $room): ?>
                    <div class="room-card"
                        data-price="<?php echo $room['price_per_night']; ?>"
                        data-type="<?php echo $room['room_size']; ?>"
                        data-availability="<?php
                                            if ($room['available_rooms'] > 0) echo 'available';
                                            elseif ($room['total_rooms'] > 0) echo 'unavailable';
                                            else echo 'coming-soon';
                                            ?>">
                        <div class="room-image">
                            <img src="<?php echo htmlspecialchars($room['image_url']); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>">

                            <!-- Availability Badge -->
                            <?php if ($room['available_rooms'] > 0): ?>
                                <div class="availability-badge available">
                                    <?php echo $room['available_rooms']; ?> Available
                                </div>
                            <?php elseif ($room['total_rooms'] > 0): ?>
                                <div class="availability-badge unavailable">
                                    Fully Booked
                                </div>
                            <?php else: ?>
                                <div class="availability-badge coming-soon">
                                    Coming Soon
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="room-info">
                            <h3><?php echo htmlspecialchars($room['name']); ?></h3>
                            <p><?php echo htmlspecialchars($room['description']); ?></p>

                            <?php if ($room['available_rooms'] > 0): ?>
                                <p>
                                    <i class="fas fa-check-circle"></i>
                                    <?php echo $room['available_rooms']; ?> room<?php echo $room['available_rooms'] > 1 ? 's' : ''; ?> available
                                </p>
                            <?php elseif ($room['total_rooms'] > 0): ?>
                                <p>
                                    <i class="fas fa-times-circle"></i>
                                    Currently fully booked (<?php echo $room['total_rooms']; ?> total rooms)
                                </p>
                            <?php else: ?>
                                <p>
                                    <i class="fas fa-clock"></i>
                                    Rooms under preparation
                                </p>
                            <?php endif; ?>

                            <p class="room-price">From $<?php echo number_format($room['price_per_night'], 2); ?>/night</p>

                            <div class="room-actions">
                                <?php if ($room['available_rooms'] > 0): ?>
                                    <a href="room-details.php?id=<?php echo $room['id']; ?>" class="room-button">View Details</a>
                                    <a href="booking.php?room_id=<?php echo $room['id']; ?>" class="room-button book-now">Book Now</a>
                                <?php elseif ($room['total_rooms'] > 0): ?>
                                    <a href="room-details.php?id=<?php echo $room['id']; ?>" class="room-button">View Details</a>
                                <?php else: ?>
                                    <a href="room-details.php?id=<?php echo $room['id']; ?>" class="room-button">View Details</a>
                                    <button class="room-button disabled" disabled>Coming Soon</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- More filtering to include availability -->

<script>
document.getElementById('filterBtn').addEventListener('click', function() {
    const roomType = document.getElementById('roomType').value;
    const priceRange = document.getElementById('priceRange').value;
    const availability = document.getElementById('availabilityFilter').value;
    const searchTerm = document.getElementById('roomSearch').value.toLowerCase();
    
    const roomCards = document.querySelectorAll('.room-card');
    let visibleCount = 0;
    
    roomCards.forEach(card => {
        let showCard = true;
        
        // Filter by room type

        if (roomType !== 'all') {
            const cardType = card.dataset.type;
            if (!cardType.toLowerCase().includes(roomType)) {
                showCard = false;
            }
        }
        
        // Filter by price range

        if (priceRange !== 'all') {
            const price = parseFloat(card.dataset.price);
            const [min, max] = priceRange.split('-').map(Number);
            if (price < min || price > max) {
                showCard = false;
            }
        }
        
        // Filter by availability

        if (availability !== 'all') {
            const cardAvailability = card.dataset.availability;
            if (cardAvailability !== availability) {
                showCard = false;
            }
        }
        
        // Filter by search term

        if (searchTerm) {
            const roomName = card.querySelector('h3').textContent.toLowerCase();
            const roomDesc = card.querySelector('p').textContent.toLowerCase();
            if (!roomName.includes(searchTerm) && !roomDesc.includes(searchTerm)) {
                showCard = false;
            }
        }
        
        card.style.display = showCard ? 'block' : 'none';
        if (showCard) visibleCount++;
    });
    
    // Update results info

    const resultsInfo = document.getElementById('searchResults');
    if (roomType !== 'all' || priceRange !== 'all' || availability !== 'all' || searchTerm) {
        resultsInfo.textContent = `Showing ${visibleCount} room(s) matching your criteria`;
        resultsInfo.style.display = 'block';
    } else {
        resultsInfo.style.display = 'none';
    }
});

// Search functionality

document.getElementById('roomSearch').addEventListener('input', function() {
    document.getElementById('filterBtn').click();
});
</script> 
 
<?php require_once 'includes/footer.php'; ?>