@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Balance Sheet as of {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</h3>
                    <div class="card-tools">
                        <form method="GET" class="form-inline">
                            <input type="date" name="date" value="{{ $date }}" class="form-control mr-2">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="mb-3">Assets</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Account Code</th>
                                        <th>Account Name</th>
                                        <th class="text-right">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($accounts['ASSET'] ?? [] as $account)
                                        <tr>
                                            <td>{{ $account->code }}</td>
                                            <td>{{ $account->name }}</td>
                                            <td class="text-right">{{ number_format($balances[$account->id] ?? 0, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="font-bold">
                                        <td colspan="2">Total Assets</td>
                                        <td class="text-right">{{ number_format($totals['ASSET'], 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h4 class="mb-3">Liabilities & Equity</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Account Code</th>
                                        <th>Account Name</th>
                                        <th class="text-right">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($accounts['LIABILITY'] ?? [] as $account)
                                        <tr>
                                            <td>{{ $account->code }}</td>
                                            <td>{{ $account->name }}</td>
                                            <td class="text-right">{{ number_format($balances[$account->id] ?? 0, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="font-bold">
                                        <td colspan="2">Total Liabilities</td>
                                        <td class="text-right">{{ number_format($totals['LIABILITY'], 2) }}</td>
                                    </tr>

                                    @foreach ($accounts['EQUITY'] ?? [] as $account)
                                        <tr>
                                            <td>{{ $account->code }}</td>
                                            <td>{{ $account->name }}</td>
                                            <td class="text-right">{{ number_format($balances[$account->id] ?? 0, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="2">Net Income</td>
                                        <td class="text-right">{{ number_format($netIncome, 2) }}</td>
                                    </tr>
                                    <tr class="font-bold">
                                        <td colspan="2">Total Equity</td>
                                        <td class="text-right">{{ number_format($totals['EQUITY'], 2) }}</td>
                                    </tr>
                                    <tr class="font-bold">
                                        <td colspan="2">Total Liabilities & Equity</td>
                                        <td class="text-right">{{ number_format($totals['LIABILITY'] + $totals['EQUITY'], 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
