      // Function to handle sorting
      function sortTable(column) {
        const urlParams = new URLSearchParams(window.location.search);
        const currentSortBy = urlParams.get('sort_by') || 'booking_date';
        const currentSortOrder = urlParams.get('sort_order') || 'DESC';

        let newSortOrder = 'ASC';
        if (currentSortBy === column && currentSortOrder === 'ASC') {
            newSortOrder = 'DESC';
        }

        urlParams.set('sort_by', column);
        urlParams.set('sort_order', newSortOrder);

        window.location.href = 'bookings.php?' + urlParams.toString();
    }