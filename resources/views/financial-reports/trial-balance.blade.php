@extends('layouts.dashboard')

@section('content')
<h1 class="text-2xl font-bold mb-4">Trial Balance as of {{ $date }}</h1>

<table class="min-w-full bg-white border border-gray-200">
    <thead>
        <tr>
            <th class="border px-4 py-2">Account Code</th>
            <th class="border px-4 py-2">Account Name</th>
            <th class="border px-4 py-2">Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($accounts as $account)
            <tr>
                <td class="border px-4 py-2">{{ $account->code }}</td>
                <td class="border px-4 py-2">{{ $account->name }}</td>
                <td class="border px-4 py-2 text-right">{{ number_format($balances[$account->id] ?? 0, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
