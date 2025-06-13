<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\TaxGroup;
use Illuminate\Http\Request;

class TaxGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taxGroups = TaxGroup::all();
        return view('settings.tax.groups.index', compact('taxGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('settings.tax.groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:tax_groups',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        TaxGroup::create($validated);

        return redirect()->route('settings.tax.groups.index')
            ->with('success', 'Tax group created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TaxGroup $taxGroup)
    {
        return view('settings.tax.groups.show', compact('taxGroup'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TaxGroup $taxGroup)
    {
        return view('settings.tax.groups.edit', compact('taxGroup'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaxGroup $taxGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:tax_groups,code,' . $taxGroup->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $taxGroup->update($validated);

        return redirect()->route('settings.tax.groups.index')
            ->with('success', 'Tax group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaxGroup $taxGroup)
    {
        if ($taxGroup->taxRates()->exists()) {
            return redirect()->route('settings.tax.groups.index')
                ->with('error', 'Cannot delete tax group because it has associated tax rates.');
        }

        $taxGroup->delete();

        return redirect()->route('settings.tax.groups.index')
            ->with('success', 'Tax group deleted successfully.');
    }
}