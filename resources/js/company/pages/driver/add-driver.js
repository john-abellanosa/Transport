document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const table = document.getElementById("dataTable");
    const noDataDiv = document.getElementById("noData");
    const storedSuccess = sessionStorage.getItem('driverSuccess');

    if (storedSuccess) {
        const successAlert = document.createElement("div");
        successAlert.className = "success_alert";
        successAlert.textContent = storedSuccess;
        document.body.prepend(successAlert);

        // Trigger slide down
        requestAnimationFrame(() => successAlert.classList.add("show"));

        // Hide after 3s
        setTimeout(() => {
            successAlert.classList.remove("show");
            successAlert.classList.add("hide");

            // Remove after slide-up animation finishes
            successAlert.addEventListener("animationend", () => {
                successAlert.remove();
            }, { once: true });
        }, 3000);

        // Clear stored message
        sessionStorage.removeItem('driverSuccess');
    }

    const addBtn = document.getElementById("addDriverBtn");
    const modal = document.getElementById("driverAddModal");
    const closeBtn = modal.querySelector(".driver-close-btn");
    const cancelBtn = modal.querySelector(".cancel-btn");
    const form = document.getElementById("driverAddForm");

    // Open modal
    addBtn.addEventListener("click", () => modal.style.display = "block");

    // Close buttons
    closeBtn.addEventListener("click", () => modal.style.display = "none");
    cancelBtn.addEventListener("click", () => {
        modal.style.display = "none";
        form.reset();
        form.querySelectorAll(".input-error").forEach(el => el.style.display = "none");
    });

    // ====== Restrict Driver Number ======
    const driverNumberInput = document.getElementById("driverNumber");
    driverNumberInput?.addEventListener("input", (e) => {
        let val = e.target.value.replace(/\D/g, "");
        if (val.length > 10) val = val.slice(0, 10);
        if (val.startsWith("0")) val = val.replace(/^0+/, "");
        e.target.value = val;
    });

    // ====== Add Driver Form Validation + Spinner ======
    form.addEventListener("submit", async function (e) {
        e.preventDefault();
        let valid = true;

        // Reset errors
        form.querySelectorAll(".input-error").forEach(el => el.style.display = "none");

        // Grab inputs
        const name = document.getElementById("driverName");
        const email = document.getElementById("driverEmail");
        const number = document.getElementById("driverNumber");
        const address = document.getElementById("driverAddress");

        const nameError = document.getElementById("driverNameError");
        const emailError = document.getElementById("driverEmailError");
        const numberError = document.getElementById("driverNumberError");
        const addressError = document.getElementById("driverAddressError");

        // === Validations ===
        if (name.value.trim() === "" || /^\s/.test(name.value)) {
            nameError.textContent = name.value.trim() === "" ? "Name is required" : "Name cannot start with a space";
            nameError.style.display = "block"; valid = false;
        }

        const emailValue = email.value.trim();
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        const allowedDomains = ["gmail.com", "yahoo.com", "outlook.com", "hotmail.com", "protonmail.com"];

        if (email.value === "") {
            emailError.textContent = "Email is required";
            emailError.style.display = "block";
            valid = false;
        } else if (/^\s/.test(email.value)) {
            emailError.textContent = "Email cannot start with a space";
            emailError.style.display = "block";
            valid = false;
        } else if (!emailRegex.test(emailValue)) {
            emailError.textContent = "Enter a valid email address";
            emailError.style.display = "block";
            valid = false;
        } else {
            const domain = emailValue.split("@")[1].toLowerCase();
            if (!allowedDomains.includes(domain)) {
                emailError.textContent = "Enter a valid email address";
                emailError.style.display = "block";
                valid = false;
            }
        }

        const phNumberRegex = /^9\d{9}$/;
        if (number.value.trim() === "" || !phNumberRegex.test(number.value.trim())) {
            numberError.textContent = number.value.trim() === "" ? "Mobile number is required" :
                "Must be a valid PH number (10 digits, start with 9)";
            numberError.style.display = "block"; valid = false;
        }
        if (address.value.trim() === "" || /^\s/.test(address.value)) {
            addressError.textContent = address.value.trim() === "" ? "Address is required" : "Address cannot start with a space";
            addressError.style.display = "block"; valid = false;
        }

        if (!valid) return;

        // ====== Show spinner on submit ======
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalHTML = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <span class="spinner" style="
                display:inline-block;
                width:16px;
                height:16px;
                border:2px solid #fff;
                border-top:2px solid transparent;
                border-radius:50%;
                margin-right:6px;
                animation: spin 0.8s linear infinite;
            "></span> Adding...
        `;

        if (!document.getElementById("spinner-style")) {
            const style = document.createElement("style");
            style.id = "spinner-style";
            style.textContent = `
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            `;
            document.head.appendChild(style);
        }

        // ====== Submit via fetch ======
        try {
            const response = await fetch(form.action, {
                method: "POST",
                body: new FormData(form),
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                    "Accept": "application/json"
                }
            });

            const result = await response.json();

            // Handle validation errors
            if (response.status === 422) {
                if (result.errors?.name) {
                    nameError.textContent = result.errors.name[0];
                    nameError.style.display = "block";
                }

                if (result.errors?.email) {
                    emailError.textContent = result.errors.email[0];
                    emailError.style.display = "block";
                }

                if (result.errors?.number) {
                    numberError.textContent = result.errors.number[0];
                    numberError.style.display = "block";
                }

                return;
            }

            // ===== Success Alert =====
            if (response.ok && result.success) {

                sessionStorage.setItem('driverSuccess', result.success);

                window.location.reload();
            }


        } catch (err) {
            console.error("Error submitting form:", err);
        } finally {
            // Reset button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHTML;
        }

    });
});