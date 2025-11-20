document.addEventListener('DOMContentLoaded', function() {
    const tableRows = document.querySelectorAll('.styled-table tbody tr');

    const companyWrapper = document.getElementById('companyDropdown');
    const companyBtn = document.getElementById('companyDropdownBtn');
    const companyMenu = document.getElementById('companyDropdownMenu');
    const companyItems = companyMenu.querySelectorAll('li');

    let selectedCompany = 'all';

    // Default active
    companyMenu.querySelector('li[data-value="all"]').classList.add('active');

    companyBtn.addEventListener('click', e => {
        e.stopPropagation();
        const open = companyWrapper.classList.toggle('open');
        companyBtn.setAttribute('aria-expanded', open ? "true" : "false");
        if (open) companyMenu.focus();
    });

    companyItems.forEach(item => {
        item.addEventListener('click', function() {
            selectedCompany = this.textContent.trim().toLowerCase(); // ✅ now includes name - branch
            filterTable();

            // Update active state
            companyItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');

            // ✅ Update button label to show current filter
            if (selectedCompany === 'all') {
                companyBtn.innerHTML = `Filter Company Name <i class="fa fa-caret-down" aria-hidden="true" style="margin-left: 5px;"></i>`;
            } else {
                const selectedText = this.textContent.trim();
                companyBtn.innerHTML = `${selectedText} <i class="fa fa-caret-down" aria-hidden="true" style="margin-left: 5px;"></i>`;
            }

            // Close dropdown
            companyWrapper.classList.remove('open');
            companyBtn.setAttribute('aria-expanded', "false");
        });
    });

    // Close when clicking outside
    document.addEventListener('click', e => {
        if (!companyWrapper.contains(e.target)) {
            companyWrapper.classList.remove('open');
            companyBtn.setAttribute('aria-expanded', "false");
        }
    });

    // ESC support
    companyMenu.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            companyWrapper.classList.remove('open');
            companyBtn.setAttribute('aria-expanded', "false");
            companyBtn.focus();
        }
    });

    // Filtering function
    function filterTable() {
        let visibleCount = 0;

        tableRows.forEach(row => {
            const companyCell = row.querySelector('td:nth-child(1)');
            const companyText = companyCell ? companyCell.textContent.trim().toLowerCase() : '';

            const companyMatch =
                (selectedCompany === 'all') ||
                (companyText === selectedCompany);

            if (companyMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Toggle "No Data"
        const noData = document.getElementById('noData');
        noData.style.display = (visibleCount === 0) ? 'block' : 'none';
    }
});
