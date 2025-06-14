<?php

namespace App\Http\Controllers\Asset;

use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetCategory::query();

        // Search by name if provided
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->get();

        return view('assets.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('assets.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:asset_categories,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'depreciation_method' => 'required|string|in:straight_line,declining_balance,sum_of_years,units_of_production',
            'default_depreciation_rate' => 'required|numeric|min:0|max:100',
            'default_useful_life' => 'required|integer|min:1',
            'status' => 'boolean'
        ]);

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        AssetCategory::create($validated);

        return redirect()->route('assets.categories.index')
            ->with('success', 'Asset category created successfully.');
    }

    public function edit(AssetCategory $category)
    {
        return view('assets.categories.edit', compact('category'));
    }

    public function update(Request $request, AssetCategory $category)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:asset_categories,code,' . $category->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'depreciation_method' => 'required|string|in:straight_line,declining_balance,sum_of_years,units_of_production',
            'default_depreciation_rate' => 'required|numeric|min:0|max:100',
            'default_useful_life' => 'required|integer|min:1',
            'status' => 'boolean'
        ]);

        $validated['updated_by'] = auth()->id();

        $category->update($validated);

        return redirect()->route('assets.categories.index')
            ->with('success', 'Asset category updated successfully.');
    }

    public function destroy(AssetCategory $category)
    {
        $category->delete();

        return redirect()->route('assets.categories.index')
            ->with('success', 'Asset category deleted successfully.');
    }
}