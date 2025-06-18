<?php

namespace App\Http\Controllers;

use App\Models\CostCentre;
use Illuminate\Http\Request;

class CostCentreController extends Controller
{
    public function index()
    {
        $costCentres = CostCentre::paginate(15);
        return view('cost-centres.index', compact('costCentres'));
    }

    public function create()
    {
        return view('cost-centres.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:cost_centres,code',
            'description' => 'nullable|string',
            'status' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        CostCentre::create($request->all());

        return redirect()->route('cost-centres.index')->with('success', 'Cost Centre created successfully.');
    }

    public function show(CostCentre $costCentre)
    {
        return view('cost-centres.show', compact('costCentre'));
    }

    public function edit(CostCentre $costCentre)
    {
        return view('cost-centres.edit', compact('costCentre'));
    }

    public function update(Request $request, CostCentre $costCentre)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:cost_centres,code,' . $costCentre->id,
            'description' => 'nullable|string',
            'status' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $costCentre->update($request->all());

        return redirect()->route('cost-centres.index')->with('success', 'Cost Centre updated successfully.');
    }

    public function destroy(CostCentre $costCentre)
    {
        $costCentre->delete();

        return redirect()->route('cost-centres.index')->with('success', 'Cost Centre deleted successfully.');
    }
}
