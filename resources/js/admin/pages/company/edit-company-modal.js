document.addEventListener("DOMContentLoaded", () => {
    const editModal = document.getElementById("editCompanyModal");
    const editForm = document.getElementById("editCompanyForm");
    const closeEditBtn = editModal?.querySelector(".company-modal-close");
    const cancelEditBtn = editModal?.querySelector(".cancel");
    const editButtons = document.querySelectorAll(".editCompanyBtn");

    // Inline error helper
    const showInlineError = (id, message) => {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = message || "";
        if (message) {
            el.classList.remove("show");
            setTimeout(() => el.classList.add("show"), 250);
        } else el.classList.remove("show");
    };

    const clearErrors = () => {
        editForm.querySelectorAll(".input-error").forEach((el) => {
            el.textContent = "";
            el.classList.remove("show");
        });
    };

    const showLocalAlert = (msg, type = "error") => {
        let alertBox = document.getElementById(`${type}-alert`);
        if (!alertBox) {
            alertBox = document.createElement("div");
            alertBox.id = `${type}-alert`;
            alertBox.className = `${type}_alert`;
            document.body.appendChild(alertBox);
        }
        alertBox.textContent = msg;
        alertBox.style.display = "block";
        alertBox.classList.add("show");
        setTimeout(() => {
            alertBox.classList.remove("show");
            alertBox.style.display = "none";
        }, 3000);
    };

    // ===============================
    // ✏️ OPEN EDIT MODAL
    // ===============================
    editButtons.forEach((btn) => {
        btn.addEventListener("click", (e) => {
            const row = e.target.closest("tr");
            if (!row) return;

            editForm.reset();
            clearErrors();

            const municipalitySelect = document.getElementById("editCompanyMunicipality");
            const selectedValue = row.dataset.municipality;

            // Restore all disabled states before enabling the current one
            Array.from(municipalitySelect.options).forEach((opt) => {
                if (opt.dataset.originallyDisabled === "true") {
                    opt.disabled = true;
                }
            });

            // Set all form fields
            document.getElementById("editCompanyId").value = row.dataset.id;
            document.getElementById("editCompanyName").value = row.dataset.name;
            document.getElementById("editCompanyBranch").value = row.dataset.branch;
            document.getElementById("editCompanyEmail").value = row.dataset.email;
            document.getElementById("editCompanyAddress").value = row.dataset.address;
            document.getElementById("editCompanyOwner").value = row.dataset.owner;
            document.getElementById("editOwnerNumber").value = row.dataset.number;
            document.getElementById("editCompanyCost").value = row.dataset.cost;

            // ✅ Allow the company’s current municipality to be selectable even if marked as “taken”
            const selectedOption = Array.from(municipalitySelect.options).find(
                (opt) => opt.value === selectedValue
            );
            if (selectedOption) {
                selectedOption.disabled = false;
                municipalitySelect.value = selectedValue;
            }

            editModal.classList.add("show");
        });
    });

    // ===============================
    // 🪟 CLOSE & CANCEL BUTTONS
    // ===============================
    const closeModal = () => {
        editModal.classList.remove("show");
        clearErrors();

        // Restore disabled options to their original state
        const municipalitySelect = document.getElementById("editCompanyMunicipality");
        Array.from(municipalitySelect.options).forEach((opt) => {
            if (opt.dataset.originallyDisabled === "true") {
                opt.disabled = true;
            }
        });
    };

    closeEditBtn?.addEventListener("click", closeModal);
    cancelEditBtn?.addEventListener("click", closeModal);

    // ===============================
    // 🚀 SUBMIT EDIT FORM
    // ===============================
    editForm?.addEventListener("submit", async (e) => {
        e.preventDefault();
        clearErrors();

        const submitBtn = editForm.querySelector('button[type="submit"]');
        const originalHTML = submitBtn.innerHTML;
        let hasError = false;

        const id = document.getElementById("editCompanyId").value.trim();
        const name = document.getElementById("editCompanyName").value.trim();
        const branch = document.getElementById("editCompanyBranch").value.trim();
        const email = document.getElementById("editCompanyEmail").value.trim();
        const address = document.getElementById("editCompanyAddress").value.trim();
        const owner = document.getElementById("editCompanyOwner").value.trim();
        const contact = document.getElementById("editOwnerNumber").value.trim();
        const municipality = document.getElementById("editCompanyMunicipality").value.trim();
        const cost = document.getElementById("editCompanyCost").value.trim();

        if (!name) {
            showInlineError("editCompanyNameError", "Company name cannot be empty");
            hasError = true;
        }
        if (!branch) {
            showInlineError("editCompanyBranchError", "Branch name cannot be empty");
            hasError = true;
        }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showInlineError("editCompanyEmailError", "Enter a valid email");
            hasError = true;
        }
        if (!address) {
            showInlineError("editCompanyAddressError", "Address cannot be empty");
            hasError = true;
        }
        if (!owner) {
            showInlineError("editCompanyOwnerError", "Owner name cannot be empty");
            hasError = true;
        }
        if (!/^\d{10}$/.test(contact)) {
            showInlineError("editOwnerNumberError", "Must be 10 digits (e.g., 9123456789)");
            hasError = true;
        }
        if (!municipality) {
            showInlineError("editCompanyMunicipalityError", "Municipality cannot be empty");
            hasError = true;
        }
        if (!cost || isNaN(cost) || Number(cost) <= 0) {
            showInlineError("editCompanyCostError", "Cost must be a positive number");
            hasError = true;
        }

        if (hasError) return;

        submitBtn.disabled = true;
        submitBtn.innerHTML = `<span class="spinner" style="
            display:inline-block;width:16px;height:16px;border:2px solid #fff;
            border-top:2px solid transparent;border-radius:50%;margin-right:5px;
            animation: spin 0.8s linear infinite;"></span> Saving...`;

        const payload = { id, name, branch, email, address, owner, contact, municipality, cost };

        try {
            const res = await fetch(`/admin/companies/${id}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN":
                        document.querySelector('meta[name="csrf-token"]')?.content || "",
                },
                body: JSON.stringify(payload),
            });

            const result = await res.json();
            if (res.ok && result.success) {
                showLocalAlert(result.success, "success");
                editModal.classList.remove("show");
                setTimeout(() => window.location.reload(), 1500);
            } else if (result.errors) {
                Object.entries(result.errors).forEach(([field, msgs]) => {
                    const map = {
                        name: "editCompanyNameError",
                        branch: "editCompanyBranchError",
                        email: "editCompanyEmailError",
                        address: "editCompanyAddressError",
                        owner: "editCompanyOwnerError",
                        contact: "editOwnerNumberError",
                        municipality: "editCompanyMunicipalityError",
                        cost: "editCompanyCostError",
                    };
                    if (map[field])
                        showInlineError(map[field], Array.isArray(msgs) ? msgs[0] : msgs);
                });
                showLocalAlert("Please fix the highlighted errors.", "error");
            } else {
                showLocalAlert(result.message || "Failed to update company.", "error");
            }
        } catch (err) {
            console.error(err);
            showLocalAlert("An unexpected error occurred.", "error");
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHTML;
        }
    });

    // 🟡 Mark which options were originally disabled on page load
    const municipalitySelect = document.getElementById("editCompanyMunicipality");
    Array.from(municipalitySelect.options).forEach((opt) => {
        if (opt.disabled) {
            opt.dataset.originallyDisabled = "true";
        }
    });
});