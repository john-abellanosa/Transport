document.addEventListener("DOMContentLoaded", function () {
    const table = document.getElementById('dataTable');
    const tbody = table.querySelector('tbody');
    const searchInput = document.getElementById('searchInput');
    const clearBtn = document.getElementById('clearSearch');
    const noResultsDiv = document.getElementById('noResultsFound');
    const noDataDiv = document.getElementById('noData'); // optional, if you have a "No Records Available" div
    const pagination = document.querySelector('.pagination');

    const companyWrapper = document.getElementById('companyDropdown');
    const companyBtn = document.getElementById('companyDropdownBtn');
    const companyMenu = document.getElementById('companyDropdownMenu');
    const companyItems = companyMenu.querySelectorAll('li');

    const fromDateInput = document.getElementById("fromDate");
    const toDateInput = document.getElementById("toDate");
    const applyFilterBtn = document.getElementById("applyFilter");
    const resetFilterBtn = document.getElementById("resetFilter");

    let selectedCompany = 'all';

    // Default active
    const allItem = companyMenu.querySelector('li[data-value="all"]');
    if (allItem) allItem.classList.add('active');

    // --- Helper functions for dates ---
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
        const raw = row.getAttribute("data-trip");
        if (raw) {
            try {
                const jsonStr = raw.replace(/&quot;/g, '"');
                const obj = JSON.parse(jsonStr);
                if (obj && obj.schedule) {
                    const iso = obj.schedule.match(/^(\d{4})-(\d{2})-(\d{2})/);
                    if (iso) return parseYMDToLocal(iso.slice(1, 4).join("-"));
                    const parsed = new Date(obj.schedule);
                    if (!isNaN(parsed))
                        return parseYMDToLocal(dateToYMD(parsed));
                }
            } catch {}
        }
        const scheduleCell = row.cells[4];
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
        const isCompanyHidden = row.dataset.companyHidden === "true";
        const isDateHidden = row.dataset.dateHidden === "true";
        row.style.display =
            !isSearchHidden && !isCompanyHidden && !isDateHidden ? "" : "none";
    }

    // --- Apply all filters ---
    function applyFilters() {
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const query = searchInput.value.trim().toLowerCase();
        const fromDate = parseYMDToLocal(fromDateInput.value);
        const toDate = parseYMDToLocal(toDateInput.value);

        if (rows.length === 0) {
            table.style.display = 'none';
            if (noDataDiv) noDataDiv.style.display = 'block';
            if (noResultsDiv) noResultsDiv.style.display = 'none';
            if (clearBtn) clearBtn.style.display = 'none';
            return;
        } else {
            table.style.display = 'table';
            if (noDataDiv) noDataDiv.style.display = 'none';
        }

        let anyVisible = false;

        rows.forEach(row => {
            // --- SEARCH ---
            const rowText = row.innerText.toLowerCase();
            const searchMatch = query === '' || rowText.includes(query);
            row.dataset.searchHidden = searchMatch ? 'false' : 'true';

            // --- COMPANY ---
            const companyCell = row.querySelector('td:nth-child(2)');
            const companyText = companyCell ? companyCell.textContent.trim().toLowerCase() : '';
            const companyMatch = selectedCompany === 'all' || companyText === selectedCompany;
            row.dataset.companyHidden = companyMatch ? 'false' : 'true';

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
            row.dataset.dateHidden = dateMatch ? 'false' : 'true';

            updateRowVisibility(row);
            if (row.style.display !== 'none') anyVisible = true;
        });

        // --- Show clear button ---
        if (clearBtn) clearBtn.style.display = query ? 'block' : 'none';

        // --- No results / no data ---
        if (anyVisible) {
            if (noResultsDiv) noResultsDiv.style.display = 'none';
        } else {
            if (noResultsDiv) noResultsDiv.style.display = 'block';
        }
    }

    // ---- SEARCH INPUT ----
    if (searchInput) searchInput.addEventListener('input', applyFilters);

    // ---- CLEAR BUTTON ----
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            searchInput.value = '';
            clearBtn.style.display = 'none';
            applyFilters();
        });
    }

    // ---- COMPANY FILTER ----
    if (companyBtn && companyWrapper) {
        companyBtn.addEventListener('click', e => {
            e.stopPropagation();
            const open = companyWrapper.classList.toggle('open');
            companyBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (open) companyMenu.focus();
        });

        companyItems.forEach(item => {
            item.addEventListener('click', function () {
                selectedCompany = this.getAttribute('data-value').trim().toLowerCase();
                companyItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                companyWrapper.classList.remove('open');
                companyBtn.setAttribute('aria-expanded', 'false');
                applyFilters();
            });
        });
    }

    // ---- DATE FILTER EVENTS ----
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

    // --- Date input restrictions ---
    fromDateInput.addEventListener("change", function () {
        const from = fromDateInput.value || "";
        toDateInput.min = from;
        if (toDateInput.value && from && toDateInput.value < from) {
            toDateInput.value = "";
        }
    });

    toDateInput.addEventListener("change", function () {
        if (fromDateInput.value && toDateInput.value < fromDateInput.value) {
            showError("“To” date cannot be earlier than “From” date.");
            toDateInput.value = "";
        }
    });

    // ---- CLOSE DROPDOWNS WHEN CLICKING OUTSIDE ----
    document.addEventListener('click', e => {
        if (!companyWrapper.contains(e.target)) {
            companyWrapper.classList.remove('open');
            companyBtn.setAttribute('aria-expanded', 'false');
        }
    });

    companyMenu.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            companyWrapper.classList.remove('open');
            companyBtn.setAttribute('aria-expanded', 'false');
            companyBtn.focus();
        }
    });

    // ---- PAGINATION / DYNAMIC ROWS ----
    if (pagination) {
        pagination.addEventListener('click', function () {
            setTimeout(() => {
                applyFilters();
            }, 150);
        });
    }

    const observer = new MutationObserver(() => applyFilters());
    observer.observe(tbody, { childList: true });

    // ---- INITIALIZE ROW DATASETS ----
    Array.from(tbody.querySelectorAll('tr')).forEach(row => {
        if (!row.dataset.searchHidden) row.dataset.searchHidden = "false";
        if (!row.dataset.companyHidden) row.dataset.companyHidden = "false";
        if (!row.dataset.dateHidden) row.dataset.dateHidden = "false";
    });

    // ---- INITIAL LOAD ----
    applyFilters();
});
