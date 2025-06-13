<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\TaxGroup;
use App\Models\TaxRate;
use Illuminate\Http\Request;

class TaxRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taxRates = TaxRate::with('taxGroup')->get();
        return view('settings.tax.rates.index', compact('taxRates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $taxGroups = TaxGroup::where('is_active', true)->get();
        return view('settings.tax.rates.create', compact('taxGroups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tax_group_id' => 'required|exists:tax_groups,id',
            'rate' => 'required|numeric|min:0|max:999.99',
            'type' => 'required|in:percentage,fixed',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        TaxRate::create($validated);

        return redirect()->route('settings.tax.rates.index')
            ->with('success', 'Tax rate created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TaxRate $taxRate)
    {
        return view('settings.tax.rates.show', compact('taxRate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TaxRate $taxRate)
    {
        $taxGroups = TaxGroup::where('is_active', true)->get();
        return view('settings.tax.rates.edit', compact('taxRate', 'taxGroups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaxRate $taxRate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tax_group_id' => 'required|exists:tax_groups,id',
            'rate' => 'required|numeric|min:0|max:999.99',
            'type' => 'required|in:percentage,fixed',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $taxRate->update($validated);

        return redirect()->route('settings.tax.rates.index')
            ->with('success', 'Tax rate updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaxRate $taxRate)
    {
        $taxRate->delete();

        return redirect()->route('settings.tax.rates.index')
            ->with('success', 'Tax rate deleted successfully.');
    }
} 