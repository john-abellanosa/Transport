document.addEventListener("DOMContentLoaded", () => {
    const deleteBtns = document.querySelectorAll(".delete"); // trash icons
    const modal = document.getElementById("deleteModal");
    if (!modal) return;

    const cancelBtn = modal.querySelector(".delete-cancel");
    const confirmBtn = modal.querySelector(".delete-confirm");

    let currentRow = null;
    let companyId = null;

    // ✅ Check if reload flag exists
    if (sessionStorage.getItem("deleteSuccess")) {
        showSuccess(sessionStorage.getItem("deleteSuccess"));
        sessionStorage.removeItem("deleteSuccess"); // clear it after showing
    }

    // Open modal
    deleteBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            currentRow = btn.closest("tr");
            companyId = currentRow?.dataset.id || null;

            if (companyId) {
                modal.classList.add("show");
            }
        });
    });

    // Close modal
    const closeModal = () => modal.classList.remove("show");
    cancelBtn.onclick = closeModal;

    // Confirm delete
    confirmBtn.onclick = async () => {
        if (!currentRow || !companyId) return;

        try {
            const res = await fetch(`/admin/companies/${companyId}`, {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                }
            });

            let data;
            try {
                data = await res.json();
            } catch {
                throw new Error("Invalid server response");
            }

            closeModal();

            if (data.success) {
                // ✅ Store success message before reload
                sessionStorage.setItem("deleteSuccess", data.success);

                currentRow.remove();
                setTimeout(() => {
                    window.location.reload();
                }, 600);
                
                if (document.querySelectorAll("#dataTable tbody tr").length === 0) {
                    document.getElementById("noData").style.display = "block";
                }
            } else if (data.error) {
                showError(data.error);
            }
        } catch (err) {
            console.error("Delete error:", err);
            closeModal();
            showError("Something went wrong while deleting the company.");
        }
    };
});

