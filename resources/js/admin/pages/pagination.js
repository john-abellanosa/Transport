document.addEventListener("DOMContentLoaded", function () {

    function setupPagination({ tableId, rowsPerPage = 10, paginationSelector, noDataSelector }) {
        const table = document.getElementById(tableId);
        if (!table) return;

        const tbody = table.querySelector("tbody");
        const rows = Array.from(tbody.querySelectorAll("tr"));
        const totalRows = rows.length;

        const pagination = document.querySelector(paginationSelector);
        if (!pagination) return;

        const noData = document.querySelector(noDataSelector);

        // --- If no rows ---
        if (totalRows === 0) {
            table.style.display = "none";
            pagination.style.display = "none";
            if (noData) noData.style.display = "block";
            return;
        } else {
            table.style.display = "table";
            if (noData) noData.style.display = "none";
        }

        const totalPages = Math.ceil(totalRows / rowsPerPage);
        let currentPage = 1;

        // --- If only one page, hide pagination completely ---
        if (totalPages <= 1) {
            pagination.style.display = "none";
            return;
        }

        // --- Otherwise, show pagination ---
        pagination.style.display = "flex";

        // Create prev/next buttons if they don't exist
        let prevBtn = pagination.querySelector(".prev");
        let nextBtn = pagination.querySelector(".next");
        let pageNumbersContainer = pagination.querySelector(".page-numbers");

        if (!prevBtn) {
            prevBtn = document.createElement("button");
            prevBtn.className = "disabled-button prev";
            prevBtn.innerHTML = '<i class="fa fa-angle-left"></i>';
            pagination.insertBefore(prevBtn, pagination.firstChild);
        }

        if (!nextBtn) {
            nextBtn = document.createElement("button");
            nextBtn.className = "active-button next";
            nextBtn.innerHTML = '<i class="fa fa-angle-right"></i>';
            pagination.appendChild(nextBtn);
        }

        if (!pageNumbersContainer) {
            pageNumbersContainer = document.createElement("div");
            pageNumbersContainer.className = "page-numbers";
            pagination.insertBefore(pageNumbersContainer, nextBtn);
        }

        function createPageButton(i) {
            const btn = document.createElement("button");
            btn.type = "button";
            btn.textContent = i;
            btn.className = i === currentPage ? "active" : "disabled";

            if (i !== currentPage) {
                btn.addEventListener("click", () => displayPage(i));
            }

            pageNumbersContainer.appendChild(btn);
        }

        function createDots() {
            const btn = document.createElement("button");
            btn.textContent = "...";
            btn.className = "disabled";
            btn.disabled = true;
            pageNumbersContainer.appendChild(btn);
        }

        function renderPagination() {
            pageNumbersContainer.innerHTML = "";

            if (totalPages <= 7) {
                for (let i = 1; i <= totalPages; i++) createPageButton(i);
            } else {
                if (currentPage <= 4) {
                    for (let i = 1; i <= 5; i++) createPageButton(i);
                    createDots();
                    createPageButton(totalPages);
                } else if (currentPage >= totalPages - 3) {
                    createPageButton(1);
                    createDots();
                    for (let i = totalPages - 4; i <= totalPages; i++) createPageButton(i);
                } else {
                    createPageButton(1);
                    createDots();
                    for (let i = currentPage - 2; i <= currentPage + 2; i++) createPageButton(i);
                    createDots();
                    createPageButton(totalPages);
                }
            }

            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages;

            prevBtn.classList.toggle("disabled-button", currentPage === 1);
            prevBtn.classList.toggle("active-button", currentPage > 1);
            nextBtn.classList.toggle("disabled-button", currentPage === totalPages);
            nextBtn.classList.toggle("active-button", currentPage < totalPages);
        }

        function displayPage(page) {
            currentPage = page;
            const start = (page - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            tbody.innerHTML = "";
            rows.slice(start, end).forEach(r => tbody.appendChild(r));

            renderPagination();
            table.scrollIntoView({ behavior: "smooth", block: "start" });
        }

        prevBtn.addEventListener("click", () => {
            if (currentPage > 1) displayPage(currentPage - 1);
        });

        nextBtn.addEventListener("click", () => {
            if (currentPage < totalPages) displayPage(currentPage + 1);
        });

        displayPage(1);
    }

    // Example usage:
    setupPagination({ 
        tableId: "dataTable", 
        rowsPerPage: 10, 
        paginationSelector: ".pagination",
        noDataSelector: "#noCompany" // Your placeholder div
    });

});
