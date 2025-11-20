document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById('searchInput');
    const clearBtn = document.getElementById('clearSearch');
    const table = document.getElementById('dataTable');
    const noResultsDiv = document.getElementById('noResultsFound');
    const tbody = table.querySelector('tbody');
    const pagination = document.querySelector('.pagination');
    let allRows = Array.from(tbody.querySelectorAll('tr'));

    function markMatches() {
        const q = searchInput.value.trim().toLowerCase();
        let anyMatch = false;
        allRows = Array.from(tbody.querySelectorAll('tr')); // 🔁 Always refresh rows
        const hasRows = allRows.length > 0;

        // Show or hide clear button
        clearBtn.style.display = q ? 'block' : 'none';

        allRows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const match = q === '' ? true : text.includes(q);
            row.dataset.searchHidden = match ? 'false' : 'true';
            row.style.display = match ? '' : 'none';
            if (match) anyMatch = true;
        });

        // ✅ Show "No Results Found" only when table has rows but none match
        if (noResultsDiv) {
            noResultsDiv.style.display = (hasRows && !anyMatch && q !== '') ? 'block' : 'none';
        }
    }

    // 🔍 Input event triggers filtering
    searchInput.addEventListener('input', markMatches);

    // 🧹 Clear search button
    clearBtn.addEventListener('click', function () {
        searchInput.value = '';
        clearBtn.style.display = 'none';
        allRows.forEach(row => {
            row.dataset.searchHidden = 'false';
            row.style.display = '';
        });
        if (noResultsDiv) noResultsDiv.style.display = 'none';
    });

    // 📄 Pagination click re-applies current filter automatically
    if (pagination) {
        pagination.addEventListener('click', function () {
            setTimeout(() => {
                allRows = Array.from(document.querySelectorAll('#dataTable tbody tr'));
                markMatches(); // 🔁 Reapply filter after page change
            }, 150);
        });
    }

    // 👁️ Watch for dynamic table changes (AJAX/pagination reload)
    const observer = new MutationObserver(() => {
        markMatches();
    });
    observer.observe(tbody, { childList: true });

    // ✅ Apply filter once on initial load
    markMatches();
});


