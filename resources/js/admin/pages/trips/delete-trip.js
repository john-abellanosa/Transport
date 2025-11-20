document.addEventListener("DOMContentLoaded", () => {
    const deleteModal = document.getElementById("deleteModal");
    const cancelBtn = deleteModal.querySelector(".delete-cancel");
    const confirmBtn = deleteModal.querySelector(".delete-confirm");
    let selectedTripId = null;

    // Open delete modal
    document.body.addEventListener("click", (e) => {
        const deleteBtn = e.target.closest(".delete");
        if (!deleteBtn) return;

        selectedTripId = deleteBtn.closest("tr").dataset.tripId;
        deleteModal.classList.add("show");
    });

    // Close modal
    cancelBtn.addEventListener("click", () => {
        deleteModal.classList.remove("show");
        selectedTripId = null;
    });

    // Confirm delete
    confirmBtn.addEventListener("click", () => {
        if (!selectedTripId) return;

        fetch(`/admin/trips/${selectedTripId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            deleteModal.classList.remove("show");

            if (data.success) {
                // ✅ Store success message in sessionStorage
                sessionStorage.setItem("deleteSuccess", data.success);
                window.location.reload();
            } else if (data.error) {
                sessionStorage.setItem("deleteError", data.error);
                window.location.reload();
            } else {
                sessionStorage.setItem("deleteError", "Failed to delete trip.");
                window.location.reload();
            }
        })
        .catch(err => {
            console.error(err);
            sessionStorage.setItem("deleteError", "An error occurred while deleting trip.");
            window.location.reload();
        });
    });

    // ✅ Show alert after reload
    const successMsg = sessionStorage.getItem("deleteSuccess");
    const errorMsg = sessionStorage.getItem("deleteError");

    if (successMsg) {
        showAlert(successMsg, "success");
        sessionStorage.removeItem("deleteSuccess");
    }

    if (errorMsg) {
        showAlert(errorMsg, "error");
        sessionStorage.removeItem("deleteError");
    }

    function showAlert(message, type) {
        const alertDiv = document.createElement("div");
        alertDiv.id = `${type}-alert`;
        alertDiv.className = type === "success" ? "success_alert" : "error_alert";
        alertDiv.innerHTML = `<strong></strong> ${message}`;
        document.body.appendChild(alertDiv);

        // auto fade out after 3s
        setTimeout(() => {
            alertDiv.style.opacity = "0";
            setTimeout(() => alertDiv.remove(), 500);
        }, 3000);
    }
});
