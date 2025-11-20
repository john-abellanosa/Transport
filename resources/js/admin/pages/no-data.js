document.addEventListener("DOMContentLoaded", () => {
    const table = document.getElementById("dataTable");
    const noData = document.getElementById("noData");

    const toggleNoData = () => {
        const hasRows = table.querySelectorAll("tbody tr").length > 0;
        if (hasRows) {
            table.style.display = "table";
            noData.style.display = "none";
        } else {
            table.style.display = "none";
            noData.style.display = "block";
        }
    };

    toggleNoData();  
});
