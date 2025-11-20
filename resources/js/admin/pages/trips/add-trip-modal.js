document.addEventListener("DOMContentLoaded", () => {
    const checkTripUrl = window.appConfig.checkTripUrl;
    const csrf = window.appConfig.csrf;

    const modal = document.getElementById("tripModal");
    const openBtn = document.getElementById("openModalBtn");
    const closeBtn = modal.querySelector(".close");
    const cancelBtn = modal.querySelector(".cancel");
    const form = document.getElementById("tripForm");
    const successBox = document.getElementById("successBox");
    const clientNumberInput = document.getElementById("clientNumber");
    const municipalityInput = document.getElementById("municipality");
    const companySelect = document.getElementById("company");
    const costInput = document.getElementById("cost");

    const confirmModal = document.getElementById("confirmDuplicateModal");
    const cancelDuplicate = document.getElementById("cancelDuplicate");
    const proceedDuplicate = document.getElementById("proceedDuplicate");

    const archivedModal = document.getElementById("archivedTripModal");
    const cancelArchived = document.getElementById("cancelArchived");
    const continueArchived = document.getElementById("continueArchived");

    const existsModal = document.getElementById("existsTripModal");
    const cancelExists = document.getElementById("cancelExists");
    const continueExists = document.getElementById("continueExists");
    const existingStatusText = document.getElementById("existingStatus");

    const archivedPendingModal = document.getElementById("archivedPendingModal");
    const cancelArchivedPending = document.getElementById("cancelArchivedPending");
    const continueArchivedPending = document.getElementById("continueArchivedPending");

    // --- Auto-fill company & cost based on municipality ---
    municipalityInput.addEventListener("change", async () => {
        const selectedMunicipality = municipalityInput.value;
        if (!selectedMunicipality) return;

        try {
            const response = await fetch(
                `/admin/municipality-cost?municipality=${encodeURIComponent(selectedMunicipality)}`
            );
            const data = await response.json();
            if (data.success) {
                companySelect.value = data.company_name;
                costInput.value = data.cost;
            } else {
                companySelect.value = "";
                costInput.value = "";
            }
        } catch (error) {
            console.error("Error fetching company info:", error);
        }
    });

    // --- Helper functions ---
    const setLoading = (btn, isLoading, text = "Processing...") => {
        if (isLoading) {
            btn.dataset.originalText = btn.textContent;
            btn.textContent = text;
            btn.disabled = true;
            btn.classList.add("loading");
        } else {
            btn.textContent = btn.dataset.originalText || "Submit";
            btn.disabled = false;
            btn.classList.remove("loading");
        }
    };

    const showInlineError = (id, message) => {
        const el = document.getElementById(id);
        if (message) (el.textContent = message), el.classList.add("show");
        else el.classList.remove("show");
    };

    const noSpaces = (str) => str && str.trim() !== "";
    const noLeadingSpace = (str) => str && str[0] !== " ";

    const clearErrors = () => {
        document.querySelectorAll(".input-error").forEach((err) => err.classList.remove("show"));
    };

    clientNumberInput.addEventListener("input", (e) => {
        let val = e.target.value.replace(/\D/g, "");
        if (val.length > 0 && val[0] === "0") val = "";
        if (val.length > 10) val = val.slice(0, 10);
        e.target.value = val;
    });

    // --- Modal open/close ---
    openBtn.onclick = () => modal.classList.add("show");
    closeBtn.onclick = () => modal.classList.remove("show");
    cancelBtn.onclick = () => {
        form.reset();
        clearErrors();
        modal.classList.remove("show");
    };

    // --- Duplicate modal buttons ---
    cancelDuplicate.addEventListener("click", () => {
        confirmModal.classList.remove("show");
        form.reset();
        clearErrors();
        modal.classList.remove("show");
        setLoading(form.querySelector(".add"), false);
    });

    proceedDuplicate.addEventListener("click", () => {
        setLoading(form.querySelector(".add"), true, "Submitting...");
        confirmModal.classList.remove("show");
        form.submit();
    });

    // --- Archived modal buttons ---
    cancelArchived.addEventListener("click", () => {
        archivedModal.classList.remove("show");
        form.reset();
        clearErrors();
        modal.classList.remove("show");
    });

    continueArchived.addEventListener("click", () => {
        archivedModal.classList.remove("show");
        form.submit();
    });

    // --- Exists modal buttons ---
    cancelExists.addEventListener("click", () => {
        existsModal.classList.remove("show");
        form.reset();
        clearErrors();
        modal.classList.remove("show");
    });

    continueExists.addEventListener("click", () => {
        existsModal.classList.remove("show");
        form.submit();
    });

    // --- Archived + Pending modal buttons ---
    cancelArchivedPending.addEventListener("click", () => {
        archivedPendingModal.classList.remove("show");
        form.reset();
        clearErrors();
        modal.classList.remove("show");
    });

    continueArchivedPending.addEventListener("click", () => {
        archivedPendingModal.classList.remove("show");
        form.submit();
    });

    function displayTransactionTags(containerId, idsArray) {
        const container = document.getElementById(containerId);
        container.innerHTML = ""; // clear previous content
        if (!idsArray || idsArray.length === 0) {
            container.innerHTML = `<span class="tag">N/A</span>`;
            return;
        }
        idsArray.forEach(id => {
            const tag = document.createElement("span");
            tag.classList.add("tag");
            tag.textContent = id;
            container.appendChild(tag);
        });
    }

    // --- Form submission ---
    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        let hasError = false;
        const addBtn = form.querySelector(".add");
        setLoading(addBtn, true, "Submitting...");

        const deliveryType = document.getElementById("deliveryType").value;
        const vehicleType = document.getElementById("vehicleType").value;
        const clientName = document.getElementById("clientName").value;
        const clientNumber = document.getElementById("clientNumber").value;
        const destination = document.getElementById("destination").value;
        const municipality = document.getElementById("municipality").value;
        const company = document.getElementById("company").value;
        const cost = document.getElementById("cost").value;
        const schedule = document.getElementById("schedule").value;

        // --- Validation ---
        if (!deliveryType) { showInlineError("deliveryTypeError", "Please select a delivery type"); hasError = true; } else showInlineError("deliveryTypeError", "");
        if (!vehicleType) { showInlineError("vehicleTypeError", "Please select a vehicle type"); hasError = true; } else showInlineError("vehicleTypeError", "");
        if (!company) { showInlineError("companyError", "Please select a company"); hasError = true; } else showInlineError("companyError", "");
        if (!noSpaces(clientName)) { showInlineError("clientNameError", "Client name cannot be empty"); hasError = true; }
        else if (!noLeadingSpace(clientName)) { showInlineError("clientNameError", "Cannot start with a space"); hasError = true; } 
        else showInlineError("clientNameError", "");
        if (!noSpaces(destination)) { showInlineError("destinationError", "Destination cannot be empty"); hasError = true; }
        else if (!noLeadingSpace(destination)) { showInlineError("destinationError", "Cannot start with a space"); hasError = true; } 
        else showInlineError("destinationError", "");
        if (!noSpaces(municipality)) { showInlineError("municipalityError", "Municipality cannot be empty"); hasError = true; }
        else if (!noLeadingSpace(municipality)) { showInlineError("municipalityError", "Cannot start with a space"); hasError = true; } 
        else showInlineError("municipalityError", "");
        if (!schedule) { showInlineError("scheduleError", "Schedule is required"); hasError = true; } else showInlineError("scheduleError", "");
        if (clientNumber === "") { showInlineError("clientNumberError", "Client number cannot be empty"); hasError = true; }
        else if (!/^\d{10}$/.test(clientNumber)) { showInlineError("clientNumberError", "Must be 10 digits"); hasError = true; } 
        else showInlineError("clientNumberError", "");
        if (cost === "") { showInlineError("costError", "Cost cannot be empty"); hasError = true; } else showInlineError("costError", "");

        if (hasError) { setLoading(addBtn, false); return; }

        try {
            const response = await fetch(checkTripUrl, {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrf },
                body: JSON.stringify({
                    deliveryType, vehicleType, clientName, clientNumber,
                    destination, municipality, company, cost, schedule
                }),
            });

            const data = await response.json();
            modal.classList.remove("show");
            setLoading(addBtn, false);

            if (data.existsSameDay) {
                switch (data.status) {
                    case "Archived":
                        displayTransactionTags("archivedTransactionIds", data.transactionIds);
                        archivedModal.classList.add("show");
                        break;
                    case "Pending":
                        existingStatusText.textContent = "Pending";
                        displayTransactionTags("existsTransactionIds", data.transactionIds);
                        existsModal.classList.add("show");
                        break;
                    case "Archived+Pending":
                        displayTransactionTags("archivedIdsList", data.archivedTransactionIds);
                        displayTransactionTags("pendingIdsList", data.pendingTransactionIds);
                        archivedPendingModal.classList.add("show");
                        break;
                    default:
                        displayTransactionTags("duplicateTransactionIds", data.transactionIds);
                        confirmModal.classList.add("show");
                }
            } else {
                form.submit();
            }

        } catch (err) {
            console.error("Error checking trip existence:", err);
            setLoading(addBtn, false);
            form.submit();
        }
    });
});
