<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Receipt;
use App\Models\Account;

class AccountingReceiptController extends Controller
{
    public function index()
    {
        $receipts = Receipt::with(['account'])
            ->latest()
            ->paginate(10);
        return view('accounting.receipts.index', compact('receipts'));
    }

    public function create()
    {
        $accounts = Account::where('is_active', true)->get();
        return view('accounting.receipts.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'receipt_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'receipt_method' => 'required|in:cash,bank_transfer,check,credit_card',
            'reference_number' => 'nullable|string',
            'customer_name' => 'required|string',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'nullable|string',
            'description' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Create the receipt
        $receipt = Receipt::create([
            'account_id' => $validated['account_id'],
            'receipt_date' => $validated['receipt_date'],
            'amount' => $validated['amount'],
            'receipt_method' => $validated['receipt_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'] ?? null,
            'customer_phone' => $validated['customer_phone'] ?? null,
            'description' => $validated['description'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update account balance
        $account = Account::findOrFail($validated['account_id']);
        $account->increment('balance', $validated['amount']);

        return redirect()->route('accounting.receipts.index')
            ->with('success', 'Receipt created successfully.');
    }

    public function show(Receipt $receipt)
    {
        $receipt->load('account');
        return view('accounting.receipts.show', compact('receipt'));
    }

    public function edit(Receipt $receipt)
    {
        $accounts = Account::where('is_active', true)->get();
        return view('accounting.receipts.edit', compact('receipt', 'accounts'));
    }

    public function update(Request $request, Receipt $receipt)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'receipt_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'receipt_method' => 'required|in:cash,bank_transfer,check,credit_card',
            'reference_number' => 'nullable|string',
            'customer_name' => 'required|string',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'nullable|string',
            'description' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Update account balances
        $oldAccount = Account::findOrFail($receipt->account_id);
        $oldAccount->decrement('balance', $receipt->amount);

        $newAccount = Account::findOrFail($validated['account_id']);
        $newAccount->increment('balance', $validated['amount']);

        // Update the receipt
        $receipt->update([
            'account_id' => $validated['account_id'],
            'receipt_date' => $validated['receipt_date'],
            'amount' => $validated['amount'],
            'receipt_method' => $validated['receipt_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'] ?? null,
            'customer_phone' => $validated['customer_phone'] ?? null,
            'description' => $validated['description'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('accounting.receipts.index')
            ->with('success', 'Receipt updated successfully.');
    }

    public function destroy(Receipt $receipt)
    {
        // Update account balance
        $account = Account::findOrFail($receipt->account_id);
        $account->decrement('balance', $receipt->amount);

        // Delete the receipt
        $receipt->delete();

        return redirect()->route('accounting.receipts.index')
            ->with('success', 'Receipt deleted successfully.');
    }
} 