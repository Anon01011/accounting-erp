@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h3 class="text-xl font-semibold text-gray-900">Trial Balance as of {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</h3>
            <form method="GET" class="flex space-x-2 items-center">
                <input type="date" name="date" value="{{ $date }}" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#01657F]">
                <button type="submit" class="bg-[#01657F] text-white px-4 py-2 rounded-md hover:bg-[#014d61]">Update</button>
            </form>
        </div>
        <div class="overflow-x-auto rounded-lg border border-[#01657F] bg-[#f9fafb]">
            <table class="min-w-full divide-y divide-[#01657F]">
                <thead class="bg-[#01657F]">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">Account Code</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">Account Name</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-white uppercase tracking-wider">Debit</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-white uppercase tracking-wider">Credit</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-white uppercase tracking-wider">Balance</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-[#01657F]">
                    @foreach ($accounts as $account)
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                <a href="{{ route('chart-of-accounts.show', $account->id) }}" class="text-[#01657F] hover:underline">
                                    {{ $account->account_code }}
                                </a>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                <a href="{{ route('chart-of-accounts.show', $account->id) }}" class="text-[#01657F] hover:underline">
                                    {{ $account->name }}
                                </a>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-right text-gray-900">{{ number_format($balances[$account->id]['debits'] ?? 0, 2) }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-right text-gray-900">{{ number_format($balances[$account->id]['credits'] ?? 0, 2) }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-right text-gray-900">{{ number_format($balances[$account->id]['balance'] ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="font-semibold bg-[#01657F]">
                        <td colspan="2" class="px-4 py-2 text-right text-white">Totals</td>
                        <td class="px-4 py-2 text-right text-white">{{ number_format($totalDebits, 2) }}</td>
                        <td class="px-4 py-2 text-right text-white">{{ number_format($totalCredits, 2) }}</td>
                        <td class="px-4 py-2 text-right text-white">{{ number_format($totalDebits - $totalCredits, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
