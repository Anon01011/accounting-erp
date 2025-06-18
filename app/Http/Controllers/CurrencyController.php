<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::paginate(15);
        return view('currencies.index', compact('currencies'));
    }

    public function create()
    {
        return view('currencies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:currencies,code',
            'symbol' => 'nullable|string|max:10',
            'exchange_rate' => 'required|numeric|min:0',
            'status' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        Currency::create($request->all());

        return redirect()->route('currencies.index')->with('success', 'Currency created successfully.');
    }

    public function show(Currency $currency)
    {
        return view('currencies.show', compact('currency'));
    }

    public function edit(Currency $currency)
    {
        return view('currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:currencies,code,' . $currency->id,
            'symbol' => 'nullable|string|max:10',
            'exchange_rate' => 'required|numeric|min:0',
            'status' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $currency->update($request->all());

        return redirect()->route('currencies.index')->with('success', 'Currency updated successfully.');
    }

    public function destroy(Currency $currency)
    {
        $currency->delete();

        return redirect()->route('currencies.index')->with('success', 'Currency deleted successfully.');
    }
}
