function autoHideAlert(alertBox) {
    setTimeout(() => {
        alertBox.classList.add("hide");
        setTimeout(() => alertBox.remove(), 500); // remove after animation
    }, 3000);
}

document.addEventListener("DOMContentLoaded", function () {
    // Auto-hide session alerts already in DOM
    document.querySelectorAll(".success_alert, .error_alert")
        .forEach(autoHideAlert);
});

window.showSuccess = function (message) {
    const alertBox = document.createElement("div");
    alertBox.className = "success_alert";
    alertBox.innerHTML = `<strong></strong> ${message}`;
    document.body.appendChild(alertBox);
    autoHideAlert(alertBox); // make it auto-hide
};

window.showError = function (message) {
    const alertBox = document.createElement("div");
    alertBox.className = "error_alert";
    alertBox.innerHTML = `<strong></strong> ${message}`;
    document.body.appendChild(alertBox);
    autoHideAlert(alertBox); // make it auto-hide
};
