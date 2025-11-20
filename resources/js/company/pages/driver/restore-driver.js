document.addEventListener("DOMContentLoaded", () => {
    const restoreModal = document.getElementById("restoreModal");
    if (!restoreModal) return;

    const cancelBtn = restoreModal.querySelector(".restore-cancel");
    const confirmBtn = restoreModal.querySelector(".restore-confirm");

    const restoreBtns = document.querySelectorAll(".restoreBtn");
    let currentRestoreForm = null;

    // Open restore modal
    restoreBtns.forEach((btn) => {
        btn.addEventListener("click", () => {
            currentRestoreForm = btn.closest(".restoreForm");
            restoreModal.classList.add("show");
        });
    });

    // Cancel button — close modal
    cancelBtn.addEventListener("click", () => {
        restoreModal.classList.remove("show");
        currentRestoreForm = null;
    });

    // Confirm button — submit restore form
    confirmBtn.addEventListener("click", () => {
        if (currentRestoreForm) {
            currentRestoreForm.submit();
        }
        restoreModal.classList.remove("show");
    });
});
