document.addEventListener('DOMContentLoaded', function() {
    const chartEl = document.getElementById('tripsChart');
    const ctx = chartEl.getContext('2d');

    // Get data from Blade via data-* attributes
    const completedTrips = JSON.parse(chartEl.dataset.completed);
    const cancelledTrips = JSON.parse(chartEl.dataset.cancelled);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            datasets: [
                {
                    label: 'Completed Trips',
                    data: completedTrips,
                    backgroundColor: '#22c55e',
                    borderColor: '#16a34a',
                    borderWidth: 1,
                    borderRadius: 6,
                    barPercentage: 0.6,
                    categoryPercentage: 0.7
                },
                {
                    label: 'Cancelled Trips',
                    data: cancelledTrips,
                    backgroundColor: '#ef4444',
                    borderColor: '#dc2626',
                    borderWidth: 1,
                    borderRadius: 6,
                    barPercentage: 0.6,
                    categoryPercentage: 0.7
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
