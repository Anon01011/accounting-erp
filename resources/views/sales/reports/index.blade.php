@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Sales Reports</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <!-- Sales Overview Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Sales Overview</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Sales</span>
                    <span class="text-lg font-semibold text-gray-900">$45,678.90</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Orders</span>
                    <span class="text-lg font-semibold text-gray-900">123</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Average Order Value</span>
                    <span class="text-lg font-semibold text-gray-900">$371.37</span>
                </div>
            </div>
        </div>

        <!-- Top Products Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Top Products</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Product A</span>
                    <span class="text-lg font-semibold text-gray-900">$12,345.67</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Product B</span>
                    <span class="text-lg font-semibold text-gray-900">$9,876.54</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Product C</span>
                    <span class="text-lg font-semibold text-gray-900">$8,765.43</span>
                </div>
            </div>
        </div>

        <!-- Top Customers Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Top Customers</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Customer A</span>
                    <span class="text-lg font-semibold text-gray-900">$5,432.10</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Customer B</span>
                    <span class="text-lg font-semibold text-gray-900">$4,321.09</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Customer C</span>
                    <span class="text-lg font-semibold text-gray-900">$3,210.98</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex space-x-4">
                    <select class="border rounded-lg px-4 py-2 focus:outline-none focus:border-theme-color">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                    <div class="relative">
                        <input type="date" class="border rounded-lg px-4 py-2 focus:outline-none focus:border-theme-color">
                    </div>
                    <div class="relative">
                        <input type="date" class="border rounded-lg px-4 py-2 focus:outline-none focus:border-theme-color">
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <button class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="h-96">
                <!-- Placeholder for chart -->
                <div class="w-full h-full bg-gray-100 rounded-lg flex items-center justify-center">
                    <span class="text-gray-500">Sales Chart</span>
                </div>
            </div>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products Sold</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Average Order Value</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <!-- Sample row - replace with actual data -->
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2024-03-21</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">10</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">25</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$3,456.78</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$345.68</td>
                </tr>
            </tbody>
        </table>

        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    Showing 1 to 1 of 1 entries
                </div>
                <div class="flex space-x-2">
                    <button class="px-3 py-1 border rounded-lg hover:bg-gray-50 disabled:opacity-50" disabled>Previous</button>
                    <button class="px-3 py-1 border rounded-lg hover:bg-gray-50 disabled:opacity-50" disabled>Next</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 