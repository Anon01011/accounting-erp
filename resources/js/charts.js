// Initialize charts when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        const revenueChart = new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Revenue',
                    data: [25000, 35000, 28000, 42000, 38000, 45000],
                    backgroundColor: '#01657F'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    // Expense Chart
    const expenseCtx = document.getElementById('expenseChart');
    if (expenseCtx) {
        const expenseChart = new Chart(expenseCtx, {
            type: 'pie',
            data: {
                labels: ['Salaries', 'Rent', 'Utilities', 'Marketing', 'Other'],
                datasets: [{
                    data: [45000, 15000, 8000, 12000, 5000],
                    backgroundColor: [
                        '#01657F',
                        '#0284c7',
                        '#0ea5e9',
                        '#38bdf8',
                        '#7dd3fc'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Cash Flow Chart
    const cashFlowCtx = document.getElementById('cashFlowChart');
    if (cashFlowCtx) {
        const cashFlowChart = new Chart(cashFlowCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                        label: 'Income',
                        data: [30000, 40000, 35000, 48000, 42000, 50000],
                        backgroundColor: '#01657F'
                    },
                    {
                        label: 'Expenses',
                        data: [25000, 35000, 28000, 42000, 38000, 45000],
                        backgroundColor: '#0284c7'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });
    }
});