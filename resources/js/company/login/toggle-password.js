document.addEventListener("DOMContentLoaded", () => {
    const pwd = document.getElementById("password");
    const cb = document.getElementById("showPassword");

    if (pwd && cb) {
        cb.addEventListener("change", () => {
            pwd.type = cb.checked ? "text" : "password";
        });
    }
});
