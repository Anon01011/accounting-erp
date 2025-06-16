@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h3 class="text-xl font-semibold text-gray-900">Balance Sheet as of {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</h3>
            <form method="GET" class="flex space-x-2 items-center">
                <input type="date" name="date" value="{{ $date }}" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#01657F]">
                <button type="submit" class="bg-[#01657F] text-white px-4 py-2 rounded-md hover:bg-[#014d61]">Update</button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-lg font-semibold mb-4">Assets</h4>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Code</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Name</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($accounts['ASSET'] ?? [] as $account)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm font-mono text-gray-900">{{ $account->account_code }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $account->name }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-right text-gray-900">{{ number_format($balances[$account->id] ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="font-semibold bg-gray-100">
                                <td colspan="2" class="px-4 py-2 text-right">Total Assets</td>
                                <td class="px-4 py-2 text-right">{{ number_format($totals['ASSET'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <h4 class="text-lg font-semibold mb-4">Liabilities & Equity</h4>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Code</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Name</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($accounts['LIABILITY'] ?? [] as $account)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm font-mono text-gray-900">{{ $account->account_code }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $account->name }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-right text-gray-900">{{ number_format($balances[$account->id] ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="font-semibold bg-gray-100">
                                <td colspan="2" class="px-4 py-2 text-right">Total Liabilities</td>
                                <td class="px-4 py-2 text-right">{{ number_format($totals['LIABILITY'], 2) }}</td>
                            </tr>

                            @foreach ($accounts['EQUITY'] ?? [] as $account)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm font-mono text-gray-900">{{ $account->account_code }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $account->name }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-right text-gray-900">{{ number_format($balances[$account->id] ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="2" class="px-4 py-2 text-right">Net Income</td>
                                <td class="px-4 py-2 text-right">{{ number_format($netIncome, 2) }}</td>
                            </tr>
                            <tr class="font-semibold bg-gray-100">
                                <td colspan="2" class="px-4 py-2 text-right">Total Equity</td>
                                <td class="px-4 py-2 text-right">{{ number_format($totals['EQUITY'], 2) }}</td>
                            </tr>
                            <tr class="font-semibold bg-gray-100">
                                <td colspan="2" class="px-4 py-2 text-right">Total Liabilities & Equity</td>
                                <td class="px-4 py-2 text-right">{{ number_format($totals['LIABILITY'] + $totals['EQUITY'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
