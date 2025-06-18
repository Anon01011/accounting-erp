<?php

namespace App\Http\Controllers;

use App\Models\LedgerAccount;
use Illuminate\Http\Request;

class LedgerAccountController extends Controller
{
    public function index()
    {
        $ledgerAccounts = LedgerAccount::paginate(15);
        return view('ledger-accounts.index', compact('ledgerAccounts'));
    }

    public function create()
    {
        return view('ledger-accounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:ledger_accounts,code',
            'description' => 'nullable|string',
        ]);

        LedgerAccount::create($request->all());

        return redirect()->route('ledger-accounts.index')->with('success', 'Ledger Account created successfully.');
    }

    public function show(LedgerAccount $ledgerAccount)
    {
        return view('ledger-accounts.show', compact('ledgerAccount'));
    }

    public function edit(LedgerAccount $ledgerAccount)
    {
        return view('ledger-accounts.edit', compact('ledgerAccount'));
    }

    public function update(Request $request, LedgerAccount $ledgerAccount)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:ledger_accounts,code,' . $ledgerAccount->id,
            'description' => 'nullable|string',
        ]);

        $ledgerAccount->update($request->all());

        return redirect()->route('ledger-accounts.index')->with('success', 'Ledger Account updated successfully.');
    }

    public function destroy(LedgerAccount $ledgerAccount)
    {
        $ledgerAccount->delete();

        return redirect()->route('ledger-accounts.index')->with('success', 'Ledger Account deleted successfully.');
    }
}
