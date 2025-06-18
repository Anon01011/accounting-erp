<?php

namespace App\Http\Controllers;

use App\Models\AccountClass;
use Illuminate\Http\Request;

class AccountClassController extends Controller
{
    public function index()
    {
        $accountClasses = AccountClass::with(['type', 'group'])->paginate(15);
        return view('account-classes.index', compact('accountClasses'));
    }

    public function create()
    {
        return view('account-classes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:account_classes,code',
            'description' => 'nullable|string',
        ]);

        AccountClass::create($request->all());

        return redirect()->route('account-classes.index')->with('success', 'Account Class created successfully.');
    }

    public function show(AccountClass $accountClass)
    {
        return view('account-classes.show', compact('accountClass'));
    }

    public function edit(AccountClass $accountClass)
    {
        return view('account-classes.edit', compact('accountClass'));
    }

    public function update(Request $request, AccountClass $accountClass)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:account_classes,code,' . $accountClass->id,
            'description' => 'nullable|string',
        ]);

        $accountClass->update($request->all());

        return redirect()->route('account-classes.index')->with('success', 'Account Class updated successfully.');
    }

    public function destroy(AccountClass $accountClass)
    {
        $accountClass->delete();

        return redirect()->route('account-classes.index')->with('success', 'Account Class deleted successfully.');
    }
}
