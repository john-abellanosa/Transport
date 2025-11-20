document.addEventListener("DOMContentLoaded", () => {
    const editModal = document.getElementById("editTripModal");
    const editForm = document.getElementById("editTripForm");
    const editCloseBtn = editModal.querySelector(".close");
    const editCancelBtn = editModal.querySelector(".cancel");
    const successBox = document.getElementById("successBox");

    const editClientNumberInput = document.getElementById("editClientNumber");
    const editMunicipalityInput = document.getElementById("editMunicipality");
    const editCompanyInput = document.getElementById("editCompany");
    const editCostInput = document.getElementById("editCost");

    // Disable past dates for schedule input
    const scheduleInput = document.getElementById("editSchedule");
    const today = new Date().toISOString().split("T")[0];  
    scheduleInput.setAttribute("min", today);

    // --- Helpers ---
    const showInlineError = (id, message) => {
        const el = document.getElementById(id);
        if (!el) return;
        if (message) {
            el.textContent = message;
            el.classList.add("show");
        } else {
            el.classList.remove("show");
        }
    };

    const showSuccess = (message) => {
        if (!successBox) return;
        successBox.textContent = message;
        successBox.classList.add("show");
        setTimeout(() => successBox.classList.remove("show"), 3000);
    };

    const noSpaces = str => str && str.trim() !== "";
    const noLeadingSpace = str => str && str[0] !== " ";

    // --- Input restrictions ---
    editClientNumberInput.addEventListener("input", e => {
        let val = e.target.value.replace(/\D/g, "");
        if (val.length > 0 && val[0] === "0") val = "";
        if (val.length > 10) val = val.slice(0, 10);
        e.target.value = val;
    });

// --- Municipality change (Edit Modal Autofill)
editMunicipalityInput.addEventListener("change", function () {
    const municipality = this.value;

    if (!municipality) {
        editCompanyInput.value = "";
        editCostInput.value = "";
        return;
    }

    // Fetch company + cost based on selected municipality
    fetch(`/admin/municipality-cost?municipality=${encodeURIComponent(municipality)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Autofill company and cost
                const companyName = data.company_name;
                const cost = data.cost;

                // Match company dropdown option
                const companyOption = Array.from(editCompanyInput.options).find(
                    opt => opt.value.trim() === companyName.trim()
                );

                if (companyOption) {
                    editCompanyInput.value = companyOption.value;
                } else {
                    // If not found, select nothing
                    editCompanyInput.value = "";
                }

                // Autofill cost field
                editCostInput.value = cost ?? "";
            } else {
                editCompanyInput.value = "";
                editCostInput.value = "";
            }
        })
        .catch(error => {
            console.error("Error fetching municipality cost:", error);
            editCompanyInput.value = "";
            editCostInput.value = "";
        });
});


    const selectedOption = editMunicipalityInput.querySelector(`option[value="${editMunicipalityInput.value}"]`);
    if (selectedOption) {
        editCompanyInput.value = selectedOption.dataset.company || "";
        editCostInput.value = selectedOption.dataset.cost || "";
    }
    document.querySelectorAll(".edit").forEach(button => {
        button.addEventListener("click", () => {
            const tr = button.closest("tr");
            if (!tr) return;

            let tripData;
            try {
                tripData = JSON.parse(tr.dataset.trip);
            } catch (err) {
                console.error("Invalid trip data JSON", err);
                return;
            }

            // Fill other modal fields
            document.getElementById("editTripId").value = tr.dataset.tripId || "";
            document.getElementById("editDeliveryType").value = tripData.deliveryType || "";
            document.getElementById("editVehicleType").value = tripData.vehicleType || "";
            document.getElementById("editClientName").value = tripData.clientName || "";
            document.getElementById("editClientNumber").value = tripData.clientNumber || "";
            document.getElementById("editDestination").value = tripData.address || "";
            document.getElementById("editMunicipality").value = tripData.municipality || "";

            // ✅ Set company dropdown
            const editCompanyInput = document.getElementById("editCompany");
            let branchLabel = tripData.branch && tripData.branch.trim() !== "" ? tripData.branch : "";
            const fullCompany = branchLabel ? `${tripData.company} - ${branchLabel}` : tripData.company;

            const option = Array.from(editCompanyInput.options).find(
                opt => opt.value.trim() === fullCompany
            );
            editCompanyInput.value = option ? option.value : "";

            // ✅ Set cost
            const editCostInput = document.getElementById("editCost");
            editCostInput.value = tripData.cost || "";

            // ✅ Set schedule date
            const scheduleInput = document.getElementById("editSchedule");
            if (tripData.schedule) {
                // Extract date part from "November 3, 2025 at 10:30am"
                const scheduleDatePart = tripData.schedule.split(" at ")[0]; // "November 3, 2025"
                const [monthName, dayWithComma, year] = scheduleDatePart.split(" ");
                const day = dayWithComma.replace(",", ""); // remove comma

                // Map month names to numbers
                const months = {
                    January: "01", February: "02", March: "03", April: "04",
                    May: "05", June: "06", July: "07", August: "08",
                    September: "09", October: "10", November: "11", December: "12"
                };

                const month = months[monthName] || "01";
                scheduleInput.value = `${year}-${month}-${day.padStart(2, "0")}`; // YYYY-MM-DD
            } else {
                scheduleInput.value = "";
            }


            // ✅ Set form action dynamically
            const editForm = document.getElementById("editTripForm");
            editForm.action = `/admin/trips/${tr.dataset.tripId}`;

            // ✅ Finally, show modal
            const editModal = document.getElementById("editTripModal");
            editModal.classList.add("show");
        });
    });


    // --- Close modal ---
    editCloseBtn.onclick = () => editModal.classList.remove("show");

    // --- Cancel button ---
    editCancelBtn.onclick = () => {
        editForm.reset();
        editForm.querySelectorAll(".input-error").forEach(err => err.classList.remove("show"));
        editModal.classList.remove("show");
    };

    // --- Form submit ---
    editForm.addEventListener("submit", e => {
        e.preventDefault();
        let hasError = false;

        const deliveryType = document.getElementById("editDeliveryType").value.trim();
        const vehicleType = document.getElementById("editVehicleType").value.trim();
        const clientName = document.getElementById("editClientName").value.trim();
        const clientNumber = document.getElementById("editClientNumber").value.trim();
        const destination = document.getElementById("editDestination").value.trim();
        const municipality = editMunicipalityInput.value.trim();
        const company = editCompanyInput.value.trim();
        const cost = editCostInput.value.trim();
        const schedule = document.getElementById("editSchedule").value.trim();

        // --- Validation ---
        if (!deliveryType) { showInlineError("editDeliveryTypeError", "Please select a delivery type"); hasError = true; } else showInlineError("editDeliveryTypeError", "");
        if (!vehicleType) { showInlineError("editVehicleTypeError", "Please select a vehicle type"); hasError = true; } else showInlineError("editVehicleTypeError", "");
        if (!company) { showInlineError("editCompanyError", "Please select a company"); hasError = true; } else showInlineError("editCompanyError", "");

        if (!noSpaces(clientName)) { showInlineError("editClientNameError", "Client name cannot be empty"); hasError = true; }
        else if (!noLeadingSpace(clientName)) { showInlineError("editClientNameError", "Cannot start with a space"); hasError = true; }
        else showInlineError("editClientNameError", "");

        if (!noSpaces(destination)) { showInlineError("editDestinationError", "Destination cannot be empty"); hasError = true; }
        else if (!noLeadingSpace(destination)) { showInlineError("editDestinationError", "Cannot start with a space"); hasError = true; }
        else showInlineError("editDestinationError", "");

        if (!noSpaces(municipality)) { showInlineError("editMunicipalityError", "Municipality cannot be empty"); hasError = true; }
        else if (!noLeadingSpace(municipality)) { showInlineError("editMunicipalityError", "Cannot start with a space"); hasError = true; }
        else showInlineError("editMunicipalityError", "");

        if (clientNumber === "") { showInlineError("editClientNumberError", "Client number cannot be empty"); hasError = true; }
        else if (!/^\d{10}$/.test(clientNumber)) { showInlineError("editClientNumberError", "Must be 10 digits (e.g., 9123456789)"); hasError = true; }
        else showInlineError("editClientNumberError", "");

        if (cost === "") { showInlineError("editCostError", "Cost cannot be empty"); hasError = true; } else showInlineError("editCostError", "");
        if (!schedule) { showInlineError("editScheduleError", "Schedule is required"); hasError = true; } else showInlineError("editScheduleError", "");

        if (hasError) return;

        // Submit form
        editForm.submit();
    });
});
