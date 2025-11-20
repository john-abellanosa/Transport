document.addEventListener("DOMContentLoaded", function () {
    const table = document.getElementById("dataTable");
    const tbody = table.querySelector("tbody");
    const pagination = document.querySelector(".pagination");
    const noResultsDiv = document.getElementById("noResultsFound");
    const noDataDiv = document.getElementById("noData"); // optional if table is empty
    const searchInput = document.getElementById("searchInput");
    const clearBtn = document.getElementById("clearSearch");
    const fromDateInput = document.getElementById("fromDate");
    const toDateInput = document.getElementById("toDate");
    const applyFilterBtn = document.getElementById("applyFilter");
    const resetFilterBtn = document.getElementById("resetFilter");

    // --- Dropdown elements ---
    const statusWrapper = document.getElementById("statusDropdown");
    const statusBtn = document.getElementById("statusDropdownBtn");
    const statusMenu = document.getElementById("statusDropdownMenu");
    const statusItems = statusMenu.querySelectorAll("li");

    const companyWrapper = document.getElementById("companyDropdown");
    const companyBtn = document.getElementById("companyDropdownBtn");
    const companyMenu = document.getElementById("companyDropdownMenu");
    const companyItems = companyMenu.querySelectorAll("li");

    let selectedStatus = "all";
    let selectedCompany = "all";

    statusMenu.querySelector('li[data-value="all"]').classList.add("active");
    companyMenu.querySelector('li[data-value="all"]').classList.add("active");

    // --- Helper for dates ---
    function parseYMDToLocal(ymd) {
        if (!ymd) return null;
        const [y, m, d] = ymd.split("-").map(Number);
        return new Date(y, m - 1, d);
    }

    function dateToYMD(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, "0");
        const d = String(date.getDate()).padStart(2, "0");
        return `${y}-${m}-${d}`;
    }

    function getRowScheduleLocalDate(row) {
        const scheduleCell = row.cells[4]; // adjust index if needed
        if (scheduleCell) {
            const txt = scheduleCell.textContent.trim();
            const parsed = new Date(txt);
            if (!isNaN(parsed)) return parseYMDToLocal(dateToYMD(parsed));
        }
        return null;
    }

    // --- Update row visibility based on all filters ---
    function updateRowVisibility(row) {
        const isSearchHidden = row.dataset.searchHidden === "true";
        const isStatusHidden = row.dataset.statusHidden === "true";
        const isCompanyHidden = row.dataset.companyHidden === "true";
        const isDateHidden = row.dataset.dateHidden === "true";

        row.style.display =
            !isSearchHidden && !isStatusHidden && !isCompanyHidden && !isDateHidden
                ? ""
                : "none";
    }

    // --- Apply all filters ---
    function applyFilters() {
        const rows = Array.from(tbody.querySelectorAll("tr"));
        const query = searchInput.value.trim().toLowerCase();
        const fromDate = parseYMDToLocal(fromDateInput.value);
        const toDate = parseYMDToLocal(toDateInput.value);

        if (rows.length === 0) {
            table.style.display = "none";
            if (noDataDiv) noDataDiv.style.display = "block";
            if (noResultsDiv) noResultsDiv.style.display = "none";
            if (clearBtn) clearBtn.style.display = "none";
            return;
        } else {
            table.style.display = "table";
            if (noDataDiv) noDataDiv.style.display = "none";
        }

        let anyVisible = false;

        rows.forEach((row) => {
            // --- SEARCH ---
            const rowText = row.innerText.toLowerCase();
            const searchMatch = query === "" || rowText.includes(query);
            row.dataset.searchHidden = searchMatch ? "false" : "true";

            // --- STATUS ---
            const statusBadge = row.querySelector(".badge");
            const statusMatch =
                selectedStatus === "all" ||
                (statusBadge && statusBadge.classList.contains(selectedStatus));
            row.dataset.statusHidden = statusMatch ? "false" : "true";

            // --- COMPANY ---
            const companyCell = row.querySelector("td:nth-child(2)");
            const companyMatch =
                selectedCompany === "all" ||
                (companyCell &&
                    companyCell.textContent.trim().toLowerCase() ===
                        selectedCompany);
            row.dataset.companyHidden = companyMatch ? "false" : "true";

            // --- DATE ---
            let dateMatch = true;
            if (fromDate || toDate) {
                const rowDate = getRowScheduleLocalDate(row);
                if (!rowDate) dateMatch = false;
                else {
                    if (fromDate && rowDate < fromDate) dateMatch = false;
                    if (toDate && rowDate > toDate) dateMatch = false;
                }
            }
            row.dataset.dateHidden = dateMatch ? "false" : "true";

            // --- Update row visibility ---
            updateRowVisibility(row);

            if (row.style.display !== "none") anyVisible = true;
        });

        // --- Show clear button ---
        if (clearBtn) clearBtn.style.display = query ? "block" : "none";

        // --- No results ---
        if (noResultsDiv)
            noResultsDiv.style.display = anyVisible ? "none" : "block";
    }

    // --- SEARCH EVENTS ---
    searchInput.addEventListener("input", applyFilters);
    clearBtn.addEventListener("click", function () {
        searchInput.value = "";
        clearBtn.style.display = "none";
        applyFilters();
    });

    // --- STATUS DROPDOWN EVENTS ---
    statusBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        const open = statusWrapper.classList.toggle("open");
        statusBtn.setAttribute("aria-expanded", open ? "true" : "false");
        if (open) statusMenu.focus();
    });

    statusItems.forEach((item) => {
        item.addEventListener("click", function () {
            selectedStatus = this.getAttribute("data-value");
            statusItems.forEach((i) => i.classList.remove("active"));
            this.classList.add("active");
            statusWrapper.classList.remove("open");
            statusBtn.setAttribute("aria-expanded", "false");
            applyFilters();
        });
    });

    // --- COMPANY DROPDOWN EVENTS ---
    companyBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        const open = companyWrapper.classList.toggle("open");
        companyBtn.setAttribute("aria-expanded", open ? "true" : "false");
        if (open) companyMenu.focus();
    });

    companyItems.forEach((item) => {
        item.addEventListener("click", function () {
            selectedCompany = this.getAttribute("data-value");
            companyItems.forEach((i) => i.classList.remove("active"));
            this.classList.add("active");
            companyWrapper.classList.remove("open");
            companyBtn.setAttribute("aria-expanded", "false");
            applyFilters();
        });
    });

    // --- DATE FILTER EVENTS ---
    applyFilterBtn.addEventListener("click", function (e) {
        e.preventDefault();
        if (!fromDateInput.value && !toDateInput.value) {
            showError("Please select both dates.");
            return;
        } else if (!fromDateInput.value) {
            showError("Please select a “From” date.");
            return;
        } else if (!toDateInput.value) {
            showError("Please select a “To” date.");
            return;
        }
        applyFilters();
    });

    resetFilterBtn.addEventListener("click", function () {
        const hadFrom = fromDateInput.value !== "";
        const hadTo = toDateInput.value !== "";
        if (!hadFrom && !hadTo) {
            showError("No date filter selected to reset.");
            return;
        }
        fromDateInput.value = "";
        toDateInput.value = "";
        toDateInput.min = "";
        applyFilters();
        showSuccess("Date filter reset successfully.");
    });

    // --- PAGINATION SUPPORT ---
    if (pagination) {
        pagination.addEventListener("click", () => {
            setTimeout(() => applyFilters(), 150);
        });
    }

    // --- MUTATION OBSERVER ---
    const observer = new MutationObserver(() => applyFilters());
    observer.observe(tbody, { childList: true });

    // --- CLOSE DROPDOWNS WHEN CLICKING OUTSIDE ---
    document.addEventListener("click", (e) => {
        if (!statusWrapper.contains(e.target)) {
            statusWrapper.classList.remove("open");
            statusBtn.setAttribute("aria-expanded", "false");
        }
        if (!companyWrapper.contains(e.target)) {
            companyWrapper.classList.remove("open");
            companyBtn.setAttribute("aria-expanded", "false");
        }
    });

    [statusMenu, companyMenu].forEach((menu) => {
        menu.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                const wrapper =
                    menu === statusMenu ? statusWrapper : companyWrapper;
                const btn = menu === statusMenu ? statusBtn : companyBtn;
                wrapper.classList.remove("open");
                btn.setAttribute("aria-expanded", "false");
                btn.focus();
            }
        });
    });

    // --- INITIALIZE ROW DATASETS ---
    Array.from(tbody.querySelectorAll("tr")).forEach((r) => {
        if (!r.dataset.searchHidden) r.dataset.searchHidden = "false";
        if (!r.dataset.statusHidden) r.dataset.statusHidden = "false";
        if (!r.dataset.companyHidden) r.dataset.companyHidden = "false";
        if (!r.dataset.dateHidden) r.dataset.dateHidden = "false";
    });

    // --- INITIAL FILTER ---
    applyFilters();
});
