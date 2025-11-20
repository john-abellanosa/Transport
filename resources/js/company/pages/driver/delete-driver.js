document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("deleteModal");
    if (!modal) return;

    const cancelBtn = modal.querySelector(".delete-cancel");
    const confirmBtn = modal.querySelector(".delete-confirm");

    let driverId = null;

    // Open modal
    document.querySelectorAll(".delete").forEach(btn => {
        btn.addEventListener("click", () => {
            driverId = btn.dataset.id;
            modal.classList.add("show");
        });
    });

    // Close modal
    cancelBtn.onclick = () => modal.classList.remove("show");

    // Confirm delete
    confirmBtn.onclick = async () => {
        if (!driverId) return;

        confirmBtn.disabled = true;
        confirmBtn.innerHTML = `<i class="fa fa-spinner fa-spin"></i> Deleting...`;

        await fetch(`/company/drivers/${driverId}`, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
            }
        });

        window.location.reload();
    };
});
