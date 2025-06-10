@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Sales Invoices</h1>
        <a href="{{ route('sales.invoices.create') }}" class="bg-theme-color text-white px-4 py-2 rounded-lg hover:bg-theme-hover">
            <i class="fas fa-plus mr-2"></i>New Invoice
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex space-x-4">
                    <div class="relative">
                        <input type="text" placeholder="Search invoices..." class="w-64 pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:border-theme-color">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                    <select class="border rounded-lg px-4 py-2 focus:outline-none focus:border-theme-color">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="sent">Sent</option>
                        <option value="paid">Paid</option>
                        <option value="overdue">Overdue</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
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

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <!-- Sample row - replace with actual data -->
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">INV-001</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">John Doe</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2024-03-21</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2024-04-21</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$1,234.56</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Paid
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div class="flex space-x-2">
                            <a href="#" class="text-theme-color hover:text-theme-hover">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="#" class="text-theme-color hover:text-theme-hover">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="text-theme-color hover:text-theme-hover">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                            <a href="#" class="text-theme-color hover:text-theme-hover">
                                <i class="fas fa-envelope"></i>
                            </a>
                            <a href="#" class="text-theme-color hover:text-theme-hover">
                                <i class="fas fa-money-bill"></i>
                            </a>
                            <a href="#" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
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