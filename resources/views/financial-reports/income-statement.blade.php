@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h3 class="text-xl font-semibold text-gray-900">Income Statement</h3>
            <form method="GET" class="flex space-x-4 items-center">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#01657F]">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date:</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#01657F]">
                </div>
                <button type="submit" class="bg-[#01657F] text-white px-4 py-2 rounded-md hover:bg-[#014d61]">Update</button>
            </form>
        </div>

        <div>
            <h4 class="text-lg font-semibold mb-4">Revenue</h4>
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Code</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Name</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($revenueAccounts as $account)
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-mono text-gray-900">{{ $account->account_code }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $account->name }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-right text-gray-900">{{ number_format($revenueBalances[$account->id] ?? 0, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="font-semibold bg-gray-100">
                            <td colspan="2" class="px-4 py-2 text-right">Total Revenue</td>
                            <td class="px-4 py-2 text-right">{{ number_format($totalRevenue, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h4 class="text-lg font-semibold mb-4 mt-8">Expenses</h4>
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Code</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Name</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($expenseAccounts as $account)
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-mono text-gray-900">{{ $account->account_code }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $account->name }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-right text-gray-900">{{ number_format($expenseBalances[$account->id] ?? 0, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="font-semibold bg-gray-100">
                            <td colspan="2" class="px-4 py-2 text-right">Total Expenses</td>
                            <td class="px-4 py-2 text-right">{{ number_format($totalExpenses, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                <table class="min-w-full divide-y divide-gray-200 rounded-lg border border-gray-200">
                    <tr class="font-semibold bg-gray-100">
                        <td class="px-4 py-2">Net Income</td>
                        <td class="px-4 py-2 text-right">{{ number_format($netIncome, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
