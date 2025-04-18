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

    // Function to cancel the booking
   function cancelBooking(bookingId) {
    if (confirm('Are you sure you want to cancel this booking?')) {
      let form = document.querySelector('.status-form[data-booking-id="${bookingId}"]');
      if (form) {
        let statusSelect = form.querySelector('select[name="booking_status"]');
        statusSelect.value = 'cancelled';
        form.submit();
      }
    }
  }
  