<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Bill;
use App\Models\Invoice;
use App\Domains\Accounting\Models\ChartOfAccount;
use App\Domains\Accounting\Models\JournalEntry;
use Illuminate\Support\Facades\DB;

class AccountingPaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['account', 'paymentable'])
            ->latest()
            ->paginate(10);
        return view('accounting.payments.index', compact('payments'));
    }

    public function create()
    {
        // Get bank and cash accounts
        $accounts = ChartOfAccount::where('is_active', true)
            ->where(function ($query) {
                $query->where('type_code', '01') // Assets
                    ->where(function ($q) {
                        $q->where('group_code', '12') // Cash Accounts
                            ->orWhere('group_code', '13'); // Bank Accounts
                    });
            })
            ->get();

        $bills = Bill::where('status', 'unpaid')->get();
        $invoices = Invoice::where('status', 'unpaid')->get();
        return view('accounting.payments.create', compact('accounts', 'bills', 'invoices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:chart_of_accounts,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,check,credit_card',
            'reference_number' => 'nullable|string',
            'paymentable_type' => 'required|in:App\Models\Bill,App\Models\Invoice',
            'paymentable_id' => 'required|integer',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            // Verify the paymentable exists and is unpaid
            $paymentable = $validated['paymentable_type']::findOrFail($validated['paymentable_id']);
            if ($paymentable->status !== 'unpaid') {
                throw new \Exception('The selected document is not unpaid.');
            }

            // Create the payment
            $payment = Payment::create([
                'account_id' => $validated['account_id'],
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'paymentable_type' => $validated['paymentable_type'],
                'paymentable_id' => $validated['paymentable_id'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create journal entries
            $description = sprintf(
                'Payment for %s #%s via %s',
                class_basename($paymentable),
                $paymentable->reference_number,
                $validated['payment_method']
            );

            // Get the accounts
            $bankAccount = ChartOfAccount::findOrFail($validated['account_id']);
            $payableAccount = ChartOfAccount::where('type_code', '02') // Liabilities
                ->where('group_code', '20') // Current Liabilities
                ->where('class_code', '10') // Accounts Payable
                ->first();

            if (!$payableAccount) {
                throw new \Exception('Accounts Payable account not found.');
            }

            // Create double entry
            JournalEntry::createDoubleEntry(
                $bankAccount->id, // Debit bank account
                $payableAccount->id, // Credit accounts payable
                $validated['amount'],
                $description,
                $payment,
                $validated['payment_date']
            );

            // Update the paymentable status
            $paymentable->update(['status' => 'paid']);
        });

        return redirect()->route('accounting.payments.index')
            ->with('success', 'Payment created successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['account', 'paymentable']);
        return view('accounting.payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        if ($payment->paymentable->status !== 'paid') {
            return redirect()->route('accounting.payments.index')
                ->with('error', 'Only payments for paid documents can be edited.');
        }

        $accounts = ChartOfAccount::where('is_active', true)
            ->where(function ($query) {
                $query->where('type_code', '01') // Assets
                    ->where(function ($q) {
                        $q->where('group_code', '12') // Cash Accounts
                            ->orWhere('group_code', '13'); // Bank Accounts
                    });
            })
            ->get();

        return view('accounting.payments.edit', compact('payment', 'accounts'));
    }

    public function update(Request $request, Payment $payment)
    {
        if ($payment->paymentable->status !== 'paid') {
            return redirect()->route('accounting.payments.index')
                ->with('error', 'Only payments for paid documents can be updated.');
        }

        $validated = $request->validate([
            'account_id' => 'required|exists:chart_of_accounts,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,check,credit_card',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($payment, $validated) {
            // Get the accounts
            $oldBankAccount = ChartOfAccount::findOrFail($payment->account_id);
            $newBankAccount = ChartOfAccount::findOrFail($validated['account_id']);
            $payableAccount = ChartOfAccount::where('type_code', '02') // Liabilities
                ->where('group_code', '20') // Current Liabilities
                ->where('class_code', '10') // Accounts Payable
                ->first();

            if (!$payableAccount) {
                throw new \Exception('Accounts Payable account not found.');
            }

            // Reverse old journal entries
            $oldDescription = sprintf(
                'Reversal of payment for %s #%s via %s',
                class_basename($payment->paymentable),
                $payment->paymentable->reference_number,
                $payment->payment_method
            );

            JournalEntry::createDoubleEntry(
                $payableAccount->id, // Debit accounts payable
                $oldBankAccount->id, // Credit old bank account
                $payment->amount,
                $oldDescription,
                $payment,
                $validated['payment_date']
            );

            // Create new journal entries
            $newDescription = sprintf(
                'Updated payment for %s #%s via %s',
                class_basename($payment->paymentable),
                $payment->paymentable->reference_number,
                $validated['payment_method']
            );

            JournalEntry::createDoubleEntry(
                $newBankAccount->id, // Debit new bank account
                $payableAccount->id, // Credit accounts payable
                $validated['amount'],
                $newDescription,
                $payment,
                $validated['payment_date']
            );

            // Update the payment
            $payment->update([
                'account_id' => $validated['account_id'],
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('accounting.payments.index')
            ->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        if ($payment->paymentable->status !== 'paid') {
            return redirect()->route('accounting.payments.index')
                ->with('error', 'Only payments for paid documents can be deleted.');
        }

        DB::transaction(function () use ($payment) {
            // Get the accounts
            $bankAccount = ChartOfAccount::findOrFail($payment->account_id);
            $payableAccount = ChartOfAccount::where('type_code', '02') // Liabilities
                ->where('group_code', '20') // Current Liabilities
                ->where('class_code', '10') // Accounts Payable
                ->first();

            if (!$payableAccount) {
                throw new \Exception('Accounts Payable account not found.');
            }

            // Create reversal journal entries
            $description = sprintf(
                'Reversal of payment for %s #%s via %s',
                class_basename($payment->paymentable),
                $payment->paymentable->reference_number,
                $payment->payment_method
            );

            JournalEntry::createDoubleEntry(
                $payableAccount->id, // Debit accounts payable
                $bankAccount->id, // Credit bank account
                $payment->amount,
                $description,
                $payment,
                $payment->payment_date
            );

            // Update paymentable status
            $payment->paymentable->update(['status' => 'unpaid']);

            // Delete the payment
            $payment->delete();
        });

        return redirect()->route('accounting.payments.index')
            ->with('success', 'Payment deleted successfully.');
    }
} 