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

function cancelBooking(bookingId) {
    if (confirm('Are you sure you want to cancel this booking?')) {
        window.location.href = 'booking-form.php?action=cancel&id=' + bookingId;
    }
}

function confirmDelete(roomId) {
  if (confirm("Are you sure you want to mark this room as deleted? This action can be reversed by an administrator.")) {
      window.location.href = "rooms.php?soft_delete=" + roomId + "&type=room";
  }
}
  
function sortUserTable(column) {
    const urlParams = new URLSearchParams(window.location.search);
    const currentSortBy = urlParams.get('sort_by') || 'id';
    const currentSortOrder = urlParams.get('sort_order') || 'ASC';

    let newSortOrder = 'ASC';
    if (currentSortBy === column && currentSortOrder === 'ASC') {
    newSortOrder = 'DESC';
    }

    urlParams.set('sort_by', column);
    urlParams.set('sort_order', newSortOrder);

    window.location.href = 'users-view.php?' + urlParams.toString();
    }

function confirmUserDelete(userId) {
    if (confirm('Are you sure you want to mark this user as deleted? This action can be reversed by an administrator.')) {
        window.location.href = 'users-view.php?soft_delete=' + userId;
    }
}
