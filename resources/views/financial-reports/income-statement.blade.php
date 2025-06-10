@extends('layouts.dashboard')

@section('content')
<h1 class="text-2xl font-bold mb-4">Income Statement</h1>

<form method="GET" class="mb-4">
    <label for="start_date" class="mr-2">Start Date:</label>
    <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="border rounded px-2 py-1 mr-4">

    <label for="end_date" class="mr-2">End Date:</label>
    <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="border rounded px-2 py-1 mr-4">

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
</form>

<h2 class="text-xl font-semibold mb-2">Revenue</h2>
<table class="min-w-full bg-white border border-gray-200 mb-6">
    <thead>
        <tr>
            <th class="border px-4 py-2">Account Code</th>
            <th class="border px-4 py-2">Account Name</th>
            <th class="border px-4 py-2">Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($revenueAccounts as $account)
            <tr>
                <td class="border px-4 py-2">{{ $account->code }}</td>
                <td class="border px-4 py-2">{{ $account->name }}</td>
                <td class="border px-4 py-2 text-right">{{ number_format($revenueBalances[$account->id] ?? 0, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h2 class="text-xl font-semibold mb-2">Expenses</h2>
<table class="min-w-full bg-white border border-gray-200">
    <thead>
        <tr>
            <th class="border px-4 py-2">Account Code</th>
            <th class="border px-4 py-2">Account Name</th>
            <th class="border px-4 py-2">Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($expenseAccounts as $account)
            <tr>
                <td class="border px-4 py-2">{{ $account->code }}</td>
                <td class="border px-4 py-2">{{ $account->name }}</td>
                <td class="border px-4 py-2 text-right">{{ number_format($expenseBalances[$account->id] ?? 0, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
