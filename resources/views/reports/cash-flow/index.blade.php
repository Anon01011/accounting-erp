@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Cash Flow Statement</h1>
        <div class="flex space-x-4">
            <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Export PDF
            </button>
            <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Export Excel
            </button>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg mb-6">
        <div class="p-6">
            <form class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg">
        <div class="p-6">
            <div class="space-y-6">
                <!-- Operating Activities -->
                <div>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Operating Activities</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Net Income</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">$50,000.00</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Depreciation</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">$5,000.00</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Net Cash from Operations</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">$55,000.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Investing Activities -->
                <div>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Investing Activities</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Purchase of Equipment</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">($20,000.00)</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Net Cash from Investing</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">($20,000.00)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Financing Activities -->
                <div>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Financing Activities</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Loan Proceeds</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">$30,000.00</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Loan Repayment</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">($10,000.00)</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Net Cash from Financing</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">$20,000.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Net Change -->
                <div>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Net Change in Cash</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr class="bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Net Increase in Cash</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">$55,000.00</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Cash at Beginning of Period</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">$100,000.00</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Cash at End of Period</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">$155,000.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 