<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaxGroup;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:tax_groups',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $taxGroup = TaxGroup::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'is_default' => $request->boolean('is_default', false)
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tax group created successfully',
                'redirect' => route('settings.tax.groups.index')
            ]);
        }

        return redirect()->route('settings.tax.groups.index')
            ->with('success', 'Tax group created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:tax_groups,name,' . $taxGroup->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $taxGroup->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'is_default' => $request->boolean('is_default', false)
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tax group updated successfully',
                'redirect' => route('settings.tax.groups.index')
            ]);
        }

        return redirect()->route('settings.tax.groups.index')
            ->with('success', 'Tax group updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaxGroup $taxGroup)
    {
        // Check if this is the default group
        if ($taxGroup->is_default) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the default tax group'
            ], 422);
        }

        $taxGroup->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tax group deleted successfully'
        ]);
    }

    public function updateStatus(Request $request, TaxGroup $taxGroup)
    {
        $validator = Validator::make($request->all(), [
            'field' => 'required|in:is_active,is_default',
            'value' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $field = $request->field;
        $value = $request->boolean('value');

        // If setting as default, ensure no other group is default
        if ($field === 'is_default' && $value) {
            TaxGroup::where('id', '!=', $taxGroup->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $taxGroup->update([$field => $value]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }
}
