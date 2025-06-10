<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CashFlowController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());
        $accountId = $request->input('account_id');

        $query = Account::query();

        if ($accountId) {
            $query->where('id', $accountId);
        }

        $accounts = $query->where('is_active', true)->get();

        $cashFlow = $this->generateCashFlow($startDate, $endDate, $accountId);

        return view('reports.cash-flow.index', compact('cashFlow', 'accounts', 'startDate', 'endDate', 'accountId'));
    }

    private function generateCashFlow($startDate, $endDate, $accountId = null)
    {
        $cashFlow = [
            'opening_balance' => 0,
            'inflows' => [],
            'outflows' => [],
            'closing_balance' => 0,
        ];

        // Get opening balance
        $openingBalanceQuery = Account::query();
        if ($accountId) {
            $openingBalanceQuery->where('id', $accountId);
        }
        $cashFlow['opening_balance'] = $openingBalanceQuery->sum('balance');

        // Get inflows (receipts)
        $receiptsQuery = Receipt::whereBetween('receipt_date', [$startDate, $endDate]);
        if ($accountId) {
            $receiptsQuery->where('account_id', $accountId);
        }
        $receipts = $receiptsQuery->get();

        foreach ($receipts as $receipt) {
            $cashFlow['inflows'][] = [
                'date' => $receipt->receipt_date,
                'description' => $receipt->description,
                'amount' => $receipt->amount,
                'type' => 'receipt',
                'reference' => $receipt->reference_number,
            ];
        }

        // Get outflows (payments)
        $paymentsQuery = Payment::whereBetween('payment_date', [$startDate, $endDate]);
        if ($accountId) {
            $paymentsQuery->where('account_id', $accountId);
        }
        $payments = $paymentsQuery->get();

        foreach ($payments as $payment) {
            $cashFlow['outflows'][] = [
                'date' => $payment->payment_date,
                'description' => $payment->paymentable_type === 'App\Models\Bill' ? 'Bill Payment' : 'Invoice Payment',
                'amount' => $payment->amount,
                'type' => 'payment',
                'reference' => $payment->reference_number,
            ];
        }

        // Calculate total inflows and outflows
        $totalInflows = collect($cashFlow['inflows'])->sum('amount');
        $totalOutflows = collect($cashFlow['outflows'])->sum('amount');

        // Calculate closing balance
        $cashFlow['closing_balance'] = $cashFlow['opening_balance'] + $totalInflows - $totalOutflows;

        // Sort transactions by date
        $allTransactions = array_merge($cashFlow['inflows'], $cashFlow['outflows']);
        usort($allTransactions, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        $cashFlow['transactions'] = $allTransactions;

        return $cashFlow;
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());
        $accountId = $request->input('account_id');

        $cashFlow = $this->generateCashFlow($startDate, $endDate, $accountId);

        // Generate CSV
        $filename = 'cash_flow_report_' . Carbon::now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($cashFlow) {
            $file = fopen('php://output', 'w');

            // Write header
            fputcsv($file, ['Cash Flow Report']);
            fputcsv($file, ['']);
            fputcsv($file, ['Opening Balance', number_format($cashFlow['opening_balance'], 2)]);
            fputcsv($file, ['']);

            // Write transactions
            fputcsv($file, ['Date', 'Description', 'Type', 'Reference', 'Amount']);

            foreach ($cashFlow['transactions'] as $transaction) {
                fputcsv($file, [
                    $transaction['date'],
                    $transaction['description'],
                    $transaction['type'],
                    $transaction['reference'],
                    number_format($transaction['amount'], 2),
                ]);
            }

            fputcsv($file, ['']);
            fputcsv($file, ['Closing Balance', number_format($cashFlow['closing_balance'], 2)]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 