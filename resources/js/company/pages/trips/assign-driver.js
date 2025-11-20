document.addEventListener("DOMContentLoaded", function() {
    const assignButtons = document.querySelectorAll(".assign");
    const assignModal = document.getElementById("assignDriverModal");
    const closeAssignModal = document.getElementById("closeAssignDriverModal");
    const cancelAssignBtn = document.getElementById("cancelAssignBtn");
    const assignTripIdInput = document.getElementById("assignTripId");
    const driverSelect = document.getElementById("driverSelect");
    const driverError = document.getElementById("driverError"); // 👈 error container

    assignButtons.forEach(btn => {
        btn.addEventListener("click", function() {
            const tripId = this.dataset.id;
            assignTripIdInput.value = tripId;
            assignModal.style.display = "block";

            // Reset error & selection when modal opens
            driverSelect.value = "";
            driverError.textContent = "";
        });
    });

    [closeAssignModal, cancelAssignBtn].forEach(el => {
        el.addEventListener("click", function() {
            assignModal.style.display = "none";
            driverError.textContent = ""; // clear error
        });
    });

    document.getElementById("assignDriverForm").addEventListener("submit", function(e) {
        e.preventDefault();
        const tripId = assignTripIdInput.value;
        const driverId = driverSelect.value;

        if (!driverId) {
            driverError.textContent = "Please select a driver."; // 👈 show error under input
            return;
        } else {
            driverError.textContent = ""; // clear if valid
        }

        fetch(`/company/trips/assign-driver`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                trip_id: tripId,
                driver_id: driverId
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.showSuccess(data.message);
                assignModal.style.display = "none";
                setTimeout(() => location.reload(), 1500);
            } else {
                window.showError(data.message || "Failed to assign driver.");
            }
        })
        .catch(err => {
            console.error(err);
            window.showError("Something went wrong!");
        });
    });
});
