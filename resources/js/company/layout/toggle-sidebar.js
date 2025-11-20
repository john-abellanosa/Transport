document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.getElementById("sidebar");
    const sidebarOverlay = document.getElementById("sidebarOverlay");
    const menuToggleBtn = document.getElementById("menuToggleBtn");
    const sidebarCloseBtn = document.getElementById("sidebarCloseBtn");

    function openSidebar() {
        sidebar.classList.add("show");
        sidebarOverlay.classList.add("visible");
        document.body.style.overflow = "hidden";
    }

    function closeSidebar() {
        sidebar.classList.remove("show");
        sidebarOverlay.classList.remove("visible");
        document.body.style.overflow = "auto";
    }

    if (menuToggleBtn) menuToggleBtn.addEventListener("click", openSidebar);
    if (sidebarCloseBtn) sidebarCloseBtn.addEventListener("click", closeSidebar);
    if (sidebarOverlay) sidebarOverlay.addEventListener("click", closeSidebar);
 
    window.closeSidebar = closeSidebar;
});