document.addEventListener("DOMContentLoaded", function () {
    const table = document.querySelector('.styled-table');
    const tbody = table.querySelector('tbody');
    const pagination = document.querySelector('.pagination');
    const noResultsDiv = document.getElementById('noResultsFound');
    const noDataDiv = document.getElementById('noData');
    const searchInput = document.getElementById('searchInput');
    const clearBtn = document.getElementById('clearSearch');

    // ---- STATUS DROPDOWN ----
    const statusWrapper = document.getElementById('statusDropdown');
    const statusBtn = document.getElementById('statusDropdownBtn');
    const statusMenu = document.getElementById('statusDropdownMenu');
    const statusItems = statusMenu.querySelectorAll('li');
    let selectedStatus = 'all';
    const allStatusItem = statusMenu.querySelector('li[data-value="all"]');
    if (allStatusItem) allStatusItem.classList.add('active');

    // ---- DATE FILTER ----
    const fromDateInput = document.getElementById("fromDate");
    const toDateInput = document.getElementById("toDate");
    const applyFilterBtn = document.getElementById("applyFilter");
    const resetFilterBtn = document.getElementById("resetFilter");

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
                const obj = JSON.parse(raw.replace(/&quot;/g, '"'));
                if (obj && obj.schedule) {
                    const iso = obj.schedule.match(/^(\d{4})-(\d{2})-(\d{2})/);
                    if (iso) return parseYMDToLocal(iso.slice(1, 4).join("-"));
                    const parsed = new Date(obj.schedule);
                    if (!isNaN(parsed)) return parseYMDToLocal(dateToYMD(parsed));
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

    // ---- Update row visibility based on all filters ----
    function updateRowVisibility(row) {
        const isSearchHidden = row.dataset.searchHidden === "true";
        const isStatusHidden = row.dataset.filterHidden === "true";
        const isDateHidden = row.dataset.dateHidden === "true";
        row.style.display =
            !isSearchHidden && !isStatusHidden && !isDateHidden ? "" : "none";
    }

    // ---- Apply all filters ----
    function applyFilters() {
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const query = searchInput.value.trim().toLowerCase();
        const fromDate = parseYMDToLocal(fromDateInput.value);
        const toDate = parseYMDToLocal(toDateInput.value);
        let anyVisible = false;

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

        rows.forEach(row => {
            // --- SEARCH ---
            const rowText = row.innerText.toLowerCase();
            const searchMatch = query === '' || rowText.includes(query);
            row.dataset.searchHidden = searchMatch ? 'false' : 'true';

            // --- STATUS ---
            const statusBadge = row.querySelector('.badge');
            const statusMatch = selectedStatus === 'all' || (statusBadge && statusBadge.classList.contains(selectedStatus));
            row.dataset.filterHidden = statusMatch ? 'false' : 'true';

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

        // --- Show/hide clear button ---
        if (clearBtn) clearBtn.style.display = query ? 'block' : 'none';

        // --- No results ---
        if (anyVisible) {
            if (noResultsDiv) noResultsDiv.style.display = 'none';
        } else {
            if (noResultsDiv) noResultsDiv.style.display = 'block';
        }
    }

    // ---- STATUS DROPDOWN EVENTS ----
    statusBtn.addEventListener('click', e => {
        e.stopPropagation();
        const open = statusWrapper.classList.toggle('open');
        statusBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) statusMenu.focus();
    });

    statusItems.forEach(item => {
        item.addEventListener('click', function () {
            selectedStatus = this.getAttribute('data-value');
            statusItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            statusWrapper.classList.remove('open');
            statusBtn.setAttribute('aria-expanded', 'false');
            applyFilters();
        });
    });

    // ---- SEARCH EVENTS ----
    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (clearBtn) clearBtn.addEventListener('click', function () {
        searchInput.value = '';
        clearBtn.style.display = 'none';
        applyFilters();
    });

    // ---- DATE FILTER EVENTS ----
    fromDateInput.addEventListener("change", function () {
        const from = fromDateInput.value || "";
        toDateInput.min = from;
        if (toDateInput.value && from && toDateInput.value < from) toDateInput.value = "";
    });

    toDateInput.addEventListener("change", function () {
        if (fromDateInput.value && toDateInput.value < fromDateInput.value) {
            showError("“To” date cannot be earlier than “From” date.");
            toDateInput.value = "";
        }
    });

    applyFilterBtn.addEventListener("click", function (e) {
        e.preventDefault();
        if (!fromDateInput.value && !toDateInput.value) {
            showError("Please select both dates."); return;
        } else if (!fromDateInput.value) {
            showError("Please select a “From” date."); return;
        } else if (!toDateInput.value) {
            showError("Please select a “To” date."); return;
        }
        applyFilters();
    });

    resetFilterBtn.addEventListener("click", function () {
        const hadFrom = fromDateInput.value !== "";
        const hadTo = toDateInput.value !== "";
        if (!hadFrom && !hadTo) {
            showError("No date filter selected to reset."); return;
        }
        fromDateInput.value = "";
        toDateInput.value = "";
        toDateInput.min = "";
        applyFilters();
        showSuccess("Date filter reset successfully.");
    });

    // ---- CLOSE DROPDOWNS ----
    document.addEventListener('click', e => {
        if (!statusWrapper.contains(e.target)) {
            statusWrapper.classList.remove('open');
            statusBtn.setAttribute('aria-expanded', 'false');
        }
    });

    statusMenu.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            statusWrapper.classList.remove('open');
            statusBtn.setAttribute('aria-expanded', 'false');
            statusBtn.focus();
        }
    });

    // ---- PAGINATION SUPPORT ----
    if (pagination) {
        pagination.addEventListener('click', function () {
            setTimeout(() => applyFilters(), 150);
        });
    }

    // ---- MUTATION OBSERVER ----
    const observer = new MutationObserver(() => applyFilters());
    observer.observe(tbody, { childList: true });

    // ---- INITIALIZE ROW DATASETS ----
    Array.from(tbody.querySelectorAll('tr')).forEach(row => {
        if (!row.dataset.searchHidden) row.dataset.searchHidden = "false";
        if (!row.dataset.filterHidden) row.dataset.filterHidden = "false";
        if (!row.dataset.dateHidden) row.dataset.dateHidden = "false";
    });

    // ---- INITIAL LOAD ----
    applyFilters();
});
