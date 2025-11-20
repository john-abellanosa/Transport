document.addEventListener("DOMContentLoaded", () => {
    const editBtns = document.querySelectorAll(".edit-btn");
    const editModal = document.getElementById("driverEditModal");
    const editForm = document.getElementById("driverEditForm");
    const editCloseBtn = document.querySelector(".driver-edit-close-btn");
    const editCancelBtn = document.querySelector(".cancel-edit-btn");
    const storedSuccess = sessionStorage.getItem('driverSuccess');

    if (storedSuccess) {
        showAlert('success', storedSuccess);
        sessionStorage.removeItem('driverSuccess'); 
    }

    // ===== Open modal =====
    editBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            const { id, name, email, number, address } = btn.dataset;
            editForm.action = `/company/drivers/${id}`;
            document.getElementById("editDriverName").value = name;
            document.getElementById("editDriverEmail").value = email;
            document.getElementById("editDriverNumber").value = number;
            document.getElementById("editDriverAddress").value = address;
            editModal.style.display = "block";
            editModal.style.opacity = "1";
        });
    });

    // ===== Close modal =====
    const closeEditModal = () => {
        editModal.style.opacity = "0";
        setTimeout(() => {
            editModal.style.display = "none";
            editForm.reset();
            editForm.querySelectorAll(".input-error").forEach(el => el.style.display = "none");
        }, 300);
    };
    editCloseBtn.addEventListener("click", closeEditModal);
    editCancelBtn.addEventListener("click", closeEditModal);

    // ===== Restrict Number Input =====
    const editDriverNumberInput = document.getElementById("editDriverNumber");
    editDriverNumberInput?.addEventListener("input", (e) => {
        let val = e.target.value.replace(/\D/g, "");
        if (val.length > 10) val = val.slice(0, 10);
        if (val.startsWith("0")) val = val.replace(/^0+/, "");
        e.target.value = val;
    });

    // ===== Form Submit with AJAX & Spinner =====
    editForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        let valid = true;

        // Reset errors
        editForm.querySelectorAll(".input-error").forEach(el => el.style.display = "none");

        const name = document.getElementById("editDriverName");
        const email = document.getElementById("editDriverEmail");
        const number = document.getElementById("editDriverNumber");
        const address = document.getElementById("editDriverAddress");

        const nameError = document.getElementById("editDriverNameError");
        const emailError = document.getElementById("editDriverEmailError");
        const numberError = document.getElementById("editDriverNumberError");
        const addressError = document.getElementById("editDriverAddressError");

        // === Local validation ===
        if (name.value.trim() === "" || /^\s/.test(name.value)) {
            nameError.textContent = name.value.trim() === "" ? "Name is required" : "Name cannot start with a space";
            nameError.style.display = "block"; valid = false;
        }

        const emailValue = email.value.trim();
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (email.value === "") {
            emailError.textContent = "Email is required";
            emailError.style.display = "block"; valid = false;
        } else if (/^\s/.test(email.value)) {
            emailError.textContent = "Email cannot start with a space";
            emailError.style.display = "block"; valid = false;
        } else if (!emailRegex.test(emailValue)) {
            emailError.textContent = "Enter a valid email address";
            emailError.style.display = "block"; valid = false;
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

        // ===== Spinner on Submit =====
        const submitBtn = editForm.querySelector('button[type="submit"]');
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
            "></span> Updating...
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

        // ===== Submit via AJAX =====
        try {
            const response = await fetch(editForm.action, {
                method: "POST",
                body: new FormData(editForm),
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                    "Accept": "application/json"
                }
            });

            if (response.status === 422) {
                const result = await response.json();

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
            } if (response.ok) {
                const result = await response.json();

                sessionStorage.setItem('driverSuccess', result.success || 'Updated successfully!');
                closeEditModal();

                window.location.reload();
            }


        } catch (err) {
            console.error("Error updating driver:", err);
            showAlert("error", "Something went wrong while updating.");
        }
        finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHTML;
        }
    });

    // ===== Show Alerts (same style as Add modal) =====
    function showAlert(type, message) {
        const alert = document.createElement("div");
        alert.className = type === "success" ? "success_alert" : "error_alert";
        alert.textContent = message;
        document.body.prepend(alert); 

        requestAnimationFrame(() => alert.classList.add("show"));

        setTimeout(() => {
            alert.classList.remove("show");
            alert.classList.add("hide");

            alert.addEventListener("animationend", () => {
                alert.remove();
            }, { once: true });
        }, 3000);
    }

});