document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("addCompanyModal");
    const openBtn = document.getElementById("openModalBtn");
    const closeBtn = modal?.querySelector(".company-modal-close");
    const cancelBtn = modal?.querySelector(".cancel");
    const form = document.getElementById("addCompanyForm");
    const ownerNumberInput = document.getElementById("ownerNumber");
    const branchNameInput = document.getElementById("branchName");
    const radioButtons = document.querySelectorAll('input[name="hasBranch"]');

    // ===============================
    // 🧾 INLINE ERROR HELPERS
    // ===============================
    const showInlineError = (id, message) => {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = message || "";
        if (message) {
            el.classList.remove("show");
            setTimeout(() => {
                el.classList.add("show");
            }, 350);
        } else {
            el.classList.remove("show");
        }
    };

    const clearErrors = () => {
        if (!form) return;
        form.querySelectorAll(".input-error").forEach((err) => {
            err.classList.remove("show");
            err.textContent = "";
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

    const showAjaxError = (msg) => showLocalAlert(msg, "error");

    const noSpaces = (s) => s && s.trim() !== "";
    const noLeadingSpace = (s) => s && s[0] !== " ";

    // ===============================
    // 📱 OWNER NUMBER FORMATTER
    // ===============================
    ownerNumberInput?.addEventListener("input", (e) => {
        let val = e.target.value.replace(/\D/g, "");
        if (val[0] === "0") val = val.slice(1);
        if (val.length > 10) val = val.slice(0, 10);
        e.target.value = val;
    });

    // ===============================
    // 🏢 RADIO BUTTON LOGIC
    // ===============================
    radioButtons.forEach((radio) => {
        radio.addEventListener("change", () => {
            if (radio.value === "yes") {
                branchNameInput.disabled = false;
                branchNameInput.style.background = "#fff";
            } else {
                branchNameInput.disabled = true;
                branchNameInput.value = "";
                branchNameInput.style.background = "#f3f4f6";
            }
        });
    });

    // ===============================
    // 🪟 MODAL BEHAVIOR
    // ===============================
    openBtn?.addEventListener("click", () => modal?.classList.add("show"));
    closeBtn?.addEventListener("click", () => modal?.classList.remove("show"));
    cancelBtn?.addEventListener("click", () => {
        form?.reset();
        clearErrors();
        branchNameInput.disabled = true;
        branchNameInput.style.background = "#f3f4f6";
        modal?.classList.remove("show");
    });

    // ===============================
    // 🚀 FORM SUBMISSION
    // ===============================
    form?.addEventListener("submit", async (e) => {
        e.preventDefault();

        let hasError = false;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalHTML = submitBtn?.innerHTML;

        // normalize & read values early
        const checkedRadio = document.querySelector(
            'input[name="hasBranch"]:checked'
        );
        const hasBranch = checkedRadio
            ? String(checkedRadio.value).trim().toLowerCase()
            : null;

        const companyName = (
            document.getElementById("companyName")?.value || ""
        ).trim();
        const branchName = (branchNameInput?.value || "").trim();
        const companyEmail = (
            document.getElementById("companyEmail")?.value || ""
        ).trim();
        const companyAddress = (
            document.getElementById("companyAddress")?.value || ""
        ).trim();
        const companyOwner = (
            document.getElementById("companyOwner")?.value || ""
        ).trim();
        const ownerNumber = (
            document.getElementById("ownerNumber")?.value || ""
        ).trim();
        const companyMunicipality = (
            document.getElementById("companyMunicipality")?.value || ""
        ).trim();
        const municipalityCost = (
            document.getElementById("municipalityCost")?.value || ""
        ).trim();

        // DEBUG (remove in production)
        // console.log("DEBUG hasBranch:", hasBranch, "companyName:", companyName);

        // Basic validations
        if (!companyName) {
            showInlineError("companyNameError", "Company name cannot be empty");
            hasError = true;
        } else showInlineError("companyNameError", "");

        if (hasBranch === "yes" && !branchName) {
            showInlineError("branchNameError", "Branch name cannot be empty");
            hasError = true;
        } else showInlineError("branchNameError", "");

        if (!noSpaces(companyEmail)) {
            showInlineError("companyEmailError", "Email cannot be empty");
            hasError = true;
        } else if (!noLeadingSpace(companyEmail)) {
            showInlineError("companyEmailError", "Cannot start with a space");
            hasError = true;
        } else {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(companyEmail)) {
                showInlineError(
                    "companyEmailError",
                    "Please enter a valid email"
                );
                hasError = true;
            } else {
                const domain = (companyEmail.split("@")[1] || "").toLowerCase();
                const allowedDomains = [
                    "gmail.com",
                    "yahoo.com",
                    "hotmail.com",
                    "outlook.com",
                ];
                if (!allowedDomains.includes(domain)) {
                    showInlineError(
                        "companyEmailError",
                        "Please enter a valid email"
                    );
                    hasError = true;
                } else {
                    showInlineError("companyEmailError", "");
                }
            }
        }

        if (!companyAddress) {
            showInlineError("companyAddressError", "Address cannot be empty");
            hasError = true;
        } else showInlineError("companyAddressError", "");

        if (!companyOwner) {
            showInlineError("companyOwnerError", "Owner name cannot be empty");
            hasError = true;
        } else showInlineError("companyOwnerError", "");

        if (!/^\d{10}$/.test(ownerNumber)) {
            showInlineError(
                "ownerNumberError",
                "Must be 10 digits (e.g., 9123456789)"
            );
            hasError = true;
        } else showInlineError("ownerNumberError", "");

        if (!companyMunicipality) {
            showInlineError(
                "companyMunicipalityError",
                "Municipality cannot be empty"
            );
            hasError = true;
        } else showInlineError("companyMunicipalityError", "");

        if (
            !municipalityCost ||
            isNaN(municipalityCost) ||
            Number(municipalityCost) <= 0
        ) {
            showInlineError(
                "municipalityCostError",
                "Cost must be a positive number"
            );
            hasError = true;
        } else showInlineError("municipalityCostError", "");

        if (hasError) return;

        // ✅ RADIO REQUIRED (must pick yes/no)
        if (hasBranch === null) {
            showLocalAlert(
                "Please choose whether the company has a branch or not.",
                "error"
            );
            return;
        }

        if (hasBranch === "yes") {
            try {
                const res = await fetch("/admin/companies/check-main", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN":
                            document.querySelector('meta[name="csrf-token"]')
                                ?.content || "",
                    },
                    body: JSON.stringify({ name: companyName }),
                });

                const data = await res.json();

                if (!data.exists) {
                    showInlineError(
                        "companyNameError",
                        "No existing main branch for this company. Please create a main branch first."
                    );
                    return; // stop form submission
                }
            } catch (err) {
                console.error("check-main error:", err);
                showInlineError(
                    "companyNameError",
                    "Error checking main company"
                );
                return;
            }
        }

        if (hasBranch === "no") {
            // Adding a new main company → exact duplicate check
            try {
                const res = await fetch("/admin/companies/check-name", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN":
                            document.querySelector('meta[name="csrf-token"]')
                                ?.content || "",
                    },
                    body: JSON.stringify({ name: companyName }),
                });

                const data = await res.json();

                if (data.exists) {
                    showInlineError(
                        "companyNameError",
                        "Company name already exists"
                    );
                    return; // stop form submission
                }
            } catch (err) {
                console.error("check-name error:", err);
                showInlineError(
                    "companyNameError",
                    "Error checking company name"
                );
                return;
            }
        }

        if (!hasError) {
            try {
                const res = await fetch("/admin/companies/check-email", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN":
                            document
                                .querySelector('meta[name="csrf-token"]')
                                ?.getAttribute("content") || "",
                    },
                    body: JSON.stringify({ email: companyEmail }),
                });
                const data = await res.json();
                if (data.exists) {
                    showInlineError(
                        "companyEmailError",
                        "Email already exists"
                    );
                    hasError = true;
                }
            } catch (err) {
                console.error(err);
                showInlineError("companyEmailError", "Error checking email");
                hasError = true;
            }
        }

        if (!hasError) {
            try {
                const res = await fetch("/admin/companies/check-contact", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN":
                            document
                                .querySelector('meta[name="csrf-token"]')
                                ?.getAttribute("content") || "",
                    },
                    body: JSON.stringify({ contact: ownerNumber }),
                });
                const data = await res.json();
                if (data.exists) {
                    showInlineError(
                        "ownerNumberError",
                        "Contact number already exists"
                    );
                    hasError = true;
                }
            } catch (err) {
                console.error(err);
                showInlineError(
                    "ownerNumberError",
                    "Error checking contact number"
                );
                hasError = true;
            }
        }

        if (!hasError) {
            try {
                const res = await fetch("/admin/companies/check-municipality", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN":
                            document
                                .querySelector('meta[name="csrf-token"]')
                                ?.getAttribute("content") || "",
                    },
                    body: JSON.stringify({ municipality: companyMunicipality }),
                });
                const data = await res.json();
                if (data.exists) {
                    showInlineError(
                        "companyMunicipalityError",
                        "Municipality already exists"
                    );
                    hasError = true;
                }
            } catch (err) {
                console.error(err);
                showInlineError(
                    "companyMunicipalityError",
                    "Error checking municipality"
                );
                hasError = true;
            }
        }

        if (hasError) return;

        // proceed to submit...
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<span class="spinner" style="
      display:inline-block;width:16px;height:16px;border:2px solid #fff;
      border-top:2px solid transparent;border-radius:50%;margin-right:5px;
      animation: spin 0.8s linear infinite;"></span> Submitting...`;

        if (!document.getElementById("spinner-style")) {
            const style = document.createElement("style");
            style.id = "spinner-style";
            style.textContent = `
        @keyframes spin { 
          0% { transform: rotate(0deg); } 
          100% { transform: rotate(360deg); } 
        }`;
            document.head.appendChild(style);
        }

        const payload = {
            name: companyName,
            branch: hasBranch === "yes" ? branchName : null,
            email: companyEmail,
            address: companyAddress,
            owner: companyOwner,
            contact: ownerNumber,
            municipality: companyMunicipality,
            cost: municipalityCost,
            has_branch: hasBranch === "yes" ? 1 : 0,
        };

        try {
            const res = await fetch("/admin/companies", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN":
                        document.querySelector('meta[name="csrf-token"]')
                            ?.content || "",
                },
                body: JSON.stringify(payload),
            });

            const raw = await res.text();
            const result = raw ? JSON.parse(raw) : {};

            if (!res.ok) {
                if (res.status === 422 && result?.errors) {
                    Object.entries(result.errors).forEach(([field, msgs]) => {
                        const map = {
                            name: "companyNameError",
                            branch: "branchNameError",
                            email: "companyEmailError",
                            address: "companyAddressError",
                            owner: "companyOwnerError",
                            contact: "ownerNumberError",
                            municipality: "companyMunicipalityError",
                            cost: "municipalityCostError",
                        };
                        const id = map[field];
                        if (id)
                            showInlineError(
                                id,
                                Array.isArray(msgs) ? msgs[0] : msgs
                            );
                    });
                    showLocalAlert(
                        "Please fix the highlighted errors.",
                        "error"
                    );
                    return;
                }
                showLocalAlert(
                    result?.message || `Request failed (HTTP ${res.status})`,
                    "error"
                );
                return;
            }

            if (result?.success) {
                showLocalAlert(result.success, "success");
                modal?.classList.remove("show");
                form.reset();
                branchNameInput.disabled = true;
                branchNameInput.style.background = "#f3f4f6";
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showLocalAlert("Unexpected server response.", "error");
            }
        } catch (err) {
            console.error(err);
            showLocalAlert("An error occurred. Please try again.", "error");
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHTML || "Submit";
        }
    });
});