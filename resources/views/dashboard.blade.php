@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Welcome Section -->
    <div class="bg-white rounded-xl shadow-sm p-8 mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Welcome back, {{ auth()->user()->name }}!</h1>
                <p class="text-gray-600 mt-2 text-lg">Here's what's happening with your accounting today.</p>
            </div>
            <div class="flex items-center space-x-3">
                <button class="group px-6 py-3 bg-theme-color text-black-500 rounded-lg hover:bg-opacity-90 transition-all duration-200 flex items-center shadow-md font-semibold hover:shadow-lg">
                    <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>New Invoice</span>
                </button>
                <button class="group px-5 py-2.5 border-2 border-theme-color text-theme-color rounded-lg hover:bg-theme-color hover:text-gray-100 transition-all duration-200 flex items-center shadow-sm font-medium hover:shadow-md">
                    <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>Export</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Revenue -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Revenue (This Month)</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format(array_sum($revenueData['data']), 2) }}</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-full">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Expenses -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Expenses (This Month)</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format(array_sum($expenseData['data']), 2) }}</p>
                </div>
                <div class="p-3 bg-red-50 rounded-full">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Net Profit -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Net Profit (This Month)</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format(array_sum($revenueData['data']) - array_sum($expenseData['data']), 2) }}</p>
                </div>
                <div class="p-3 bg-green-50 rounded-full">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Employees -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Employees</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalEmployees }}</p>
                </div>
                <div class="p-3 bg-yellow-50 rounded-full">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory, Sales, Purchases, Payroll KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Products</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalProducts }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Stock Value</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stockValue, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Low Stock Items</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $lowStock }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Payroll (This Month)</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($payrollThisMonth, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales & Purchases KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Sales (This Month)</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($salesThisMonth, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Purchases (This Month)</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($purchasesThisMonth, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Customers & Suppliers -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Top Customers (This Month)</h2>
            <ul>
                @foreach($topCustomers as $customer)
                    <li class="flex justify-between py-2 border-b last:border-b-0">
                        <span>{{ $customer->name }}</span>
                        <span class="font-mono">{{ number_format($customer->total_sales, 2) }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Top Suppliers (This Month)</h2>
            <ul>
                @foreach($topSuppliers as $supplier)
                    <li class="flex justify-between py-2 border-b last:border-b-0">
                        <span>{{ $supplier->name }}</span>
                        <span class="font-mono">{{ number_format($supplier->total_purchases, 2) }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Revenue Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Revenue Overview</h2>
            <div class="h-80">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Expense Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Expense Breakdown</h2>
            <div class="h-80">
                <canvas id="expenseChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Cash Flow Chart -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Cash Flow</h2>
        <div class="h-80">
            <canvas id="cashFlowChart"></canvas>
        </div>
    </div>

    <!-- Recent Activity and Tasks -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Recent Activity</h2>
                <a href="#" class="text-sm text-theme-color hover:underline font-medium">View All</a>
            </div>
            <div class="space-y-4">
                <div class="flex items-center p-4 hover:bg-gray-50 rounded-lg transition">
                    <div class="p-3 bg-blue-50 rounded-full">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-900">New Invoice Created</p>
                        <p class="text-sm text-gray-600">Invoice #INV-2024-001 for ₹25,000</p>
                    </div>
                    <span class="ml-auto text-sm text-gray-500">2h ago</span>
                </div>
                <div class="flex items-center p-4 hover:bg-gray-50 rounded-lg transition">
                    <div class="p-3 bg-green-50 rounded-full">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-900">Payment Received</p>
                        <p class="text-sm text-gray-600">Payment of ₹15,000 for Invoice #INV-2024-000</p>
                    </div>
                    <span class="ml-auto text-sm text-gray-500">5h ago</span>
                </div>
            </div>
        </div>

        <!-- Upcoming Tasks -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Upcoming Tasks</h2>
                <a href="#" class="text-sm text-theme-color hover:underline font-medium">View All</a>
            </div>
            <div class="space-y-4">
                <div class="flex items-center p-4 hover:bg-gray-50 rounded-lg transition">
                    <input type="checkbox" class="w-4 h-4 text-theme-color rounded border-gray-300 focus:ring-theme-color">
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-900">Follow up on Invoice #INV-2024-001</p>
                        <p class="text-sm text-gray-600">Due in 2 days</p>
                    </div>
                </div>
                <div class="flex items-center p-4 hover:bg-gray-50 rounded-lg transition">
                    <input type="checkbox" class="w-4 h-4 text-theme-color rounded border-gray-300 focus:ring-theme-color">
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-900">Prepare monthly financial report</p>
                        <p class="text-sm text-gray-600">Due in 5 days</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Wait for the DOM to be fully loaded
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
                datasets: [
                    {
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
</script>
@endpush
@endsection 