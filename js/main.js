document.addEventListener('DOMContentLoaded', function() {
    const roomSelect = document.getElementById('room_id');
    const guestSelect = document.getElementById('numGuests');
    const arrivalDate = document.getElementById('arriveDate');
    const departureDate = document.getElementById('departDate');
    const numNights = document.getElementById('numNights');
    const totalPriceSpan = document.getElementById('totalPrice');

    function updateGuestOptions() {
        const selectedRoom = roomSelect.options[roomSelect.selectedIndex];
        const capacity = selectedRoom.getAttribute('data-capacity');
        
        guestSelect.innerHTML = '<option value="">-- Select Number of Guests --</option>';
        
        if (capacity) {
            for (let i = 1; i <= parseInt(capacity); i++) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = i + (i === 1 ? ' guest' : ' guests');
                guestSelect.appendChild(option);
            }
            guestSelect.disabled = false;
        } else {
            guestSelect.disabled = true;
        }
        
        guestSelect.value = '';
        calculateTotal();
    }

    // Calculate number of nights

    function calculateNights() {
        if (arrivalDate.value && departureDate.value) {
            const arrival = new Date(arrivalDate.value);
            const departure = new Date(departureDate.value);
            const timeDiff = departure.getTime() - arrival.getTime();
            const nights = Math.ceil(timeDiff / (1000 * 3600 * 24));
            
            if (nights > 0) {
                numNights.value = nights;
            } else {
                numNights.value = 1;
                if (nights <= 0) {
                    alert('Departure date must be after arrival date');
                    departureDate.value = '';
                }
            }
        }
        calculateTotal();
    }

    // Calculate total price

    function calculateTotal() {
        const selectedRoom = roomSelect.options[roomSelect.selectedIndex];
        const pricePerNight = selectedRoom.getAttribute('data-price');
        const nights = parseInt(numNights.value) || 0;
        
        if (pricePerNight && nights > 0) {
            const total = parseFloat(pricePerNight) * nights;
            totalPriceSpan.textContent = total.toFixed(2);
        } else {
            totalPriceSpan.textContent = '0.00';
        }
    }

    // Departure date

    function updateMinDepartureDate() {
        if (arrivalDate.value) {
            const arrival = new Date(arrivalDate.value);
            arrival.setDate(arrival.getDate() + 1);
            const minDeparture = arrival.toISOString().split('T')[0];
            departureDate.min = minDeparture;
            
            if (departureDate.value && new Date(departureDate.value) <= new Date(arrivalDate.value)) {
                departureDate.value = '';
            }
        }
        calculateNights();
    }

    roomSelect.addEventListener('change', updateGuestOptions);
    arrivalDate.addEventListener('change', updateMinDepartureDate);
    departureDate.addEventListener('change', calculateNights);

    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        const selectedRoom = roomSelect.options[roomSelect.selectedIndex];
        const capacity = parseInt(selectedRoom.getAttribute('data-capacity'));
        const guests = parseInt(guestSelect.value);
        
        if (guests > capacity) {
            e.preventDefault();
            alert(`Selected room can only accommodate ${capacity} guest${capacity === 1 ? '' : 's'}.`);
            return false;
        }

        // Validate date

        if (arrivalDate.value && departureDate.value) {
            const arrival = new Date(arrivalDate.value);
            const departure = new Date(departureDate.value);
            
            if (departure <= arrival) {
                e.preventDefault();
                alert('Departure date must be after arrival date.');
                return false;
            }
        }
    });

    updateGuestOptions();
    calculateTotal();
});

document.addEventListener('DOMContentLoaded', function() {
    const userMenu = document.querySelector('.user-menu');
    if (userMenu) {
        const userLink = userMenu.querySelector('.user-link');
        const userDropdown = userMenu.querySelector('.user-dropdown');

        if (userLink && userDropdown) {
            userLink.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (userDropdown.style.display === 'block') {
                    userDropdown.style.display = 'none';
                } else {
                    userDropdown.style.display = 'block';
                }
            });

            document.addEventListener('click', function(e) {
                if (!userMenu.contains(e.target)) {
                    userDropdown.style.display = 'none';
                }
            });
        }
    }
});