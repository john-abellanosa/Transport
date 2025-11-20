document.addEventListener("DOMContentLoaded", () => {
    const logoutModal = document.getElementById('logoutModal');
    const logoutCancelBtn = logoutModal.querySelector('.logout-cancel');
    const logoutLink = document.querySelector('.logout-link');

    // Open modal when logout link is clicked
    logoutLink.addEventListener('click', (e) => {
        e.preventDefault(); // Prevent default navigation
        logoutModal.classList.add('show');
    });

    // Cancel → close modal
    logoutCancelBtn.addEventListener('click', () => {
        logoutModal.classList.remove('show');
    });
});
