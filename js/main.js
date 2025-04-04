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