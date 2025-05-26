function updateNights() {
    const arriveDate = new Date(document.getElementById("arriveDate").value);
    const departDate = new Date(document.getElementById("departDate").value);
    if (!isNaN(arriveDate) && !isNaN(departDate) && departDate > arriveDate) {
        const timeDiff = departDate - arriveDate;
        const nights = Math.ceil(timeDiff / (1000 * 3600 * 24));
        document.getElementById("numNights").value = nights;
    } else {
        document.getElementById("numNights").value = 1;
    }
    calculateTotal();
}

function calculateTotal() {
    const pricePerNight = parseInt(document.getElementById("roomType").value);
    const numNights = parseInt(document.getElementById("numNights").value);
    const totalPrice = pricePerNight * numNights;
    document.getElementById("totalPrice").textContent = totalPrice;
}

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