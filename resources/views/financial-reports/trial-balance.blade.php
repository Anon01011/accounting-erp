@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Trial Balance as of {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</h3>
                    <div class="card-tools">
                        <form method="GET" class="form-inline">
                            <input type="date" name="date" value="{{ $date }}" class="form-control mr-2">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Account Code</th>
                                <th>Account Name</th>
                                <th class="text-right">Debit</th>
                                <th class="text-right">Credit</th>
                                <th class="text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($accounts as $account)
                                <tr>
                                    <td>{{ $account->code }}</td>
                                    <td>{{ $account->name }}</td>
                                    <td class="text-right">{{ number_format($balances[$account->id]['debits'] ?? 0, 2) }}</td>
                                    <td class="text-right">{{ number_format($balances[$account->id]['credits'] ?? 0, 2) }}</td>
                                    <td class="text-right">{{ number_format($balances[$account->id]['balance'] ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="font-bold">
                                <td colspan="2">Totals</td>
                                <td class="text-right">{{ number_format($totalDebits, 2) }}</td>
                                <td class="text-right">{{ number_format($totalCredits, 2) }}</td>
                                <td class="text-right">{{ number_format($totalDebits - $totalCredits, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
