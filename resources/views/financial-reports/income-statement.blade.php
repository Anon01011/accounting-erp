@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Income Statement</h3>
                    <div class="card-tools">
                        <form method="GET" class="form-inline">
                            <div class="form-group mr-2">
                                <label for="start_date" class="mr-2">Start Date:</label>
                                <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="form-control">
                            </div>
                            <div class="form-group mr-2">
                                <label for="end_date" class="mr-2">End Date:</label>
                                <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="mb-3">Revenue</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Account Code</th>
                                        <th>Account Name</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($revenueAccounts as $account)
                                        <tr>
                                            <td>{{ $account->code }}</td>
                                            <td>{{ $account->name }}</td>
                                            <td class="text-right">{{ number_format($revenueBalances[$account->id] ?? 0, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="font-bold">
                                        <td colspan="2">Total Revenue</td>
                                        <td class="text-right">{{ number_format($totalRevenue, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <h4 class="mb-3 mt-4">Expenses</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Account Code</th>
                                        <th>Account Name</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expenseAccounts as $account)
                                        <tr>
                                            <td>{{ $account->code }}</td>
                                            <td>{{ $account->name }}</td>
                                            <td class="text-right">{{ number_format($expenseBalances[$account->id] ?? 0, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="font-bold">
                                        <td colspan="2">Total Expenses</td>
                                        <td class="text-right">{{ number_format($totalExpenses, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="mt-4">
                                <table class="table table-bordered">
                                    <tr class="font-bold">
                                        <td>Net Income</td>
                                        <td class="text-right">{{ number_format($netIncome, 2) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
