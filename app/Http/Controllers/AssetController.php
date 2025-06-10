<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
    public function __construct()
    {
        // Add middleware for access control if needed
        $this->middleware('auth');
    }

    public function index()
    {
        $assets = ChartOfAccount::where('type_code', '01') // Assuming 01 is the code for Assets
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('account_code')
            ->get();

        $assetCategories = config('accounting.asset_categories');
        $parentAssets = ChartOfAccount::where('type_code', '01')
            ->orderBy('account_code')
            ->get();

        return view('assets.index', compact('assets', 'assetCategories', 'parentAssets'));
    }

    public function create()
    {
        $parentAssets = ChartOfAccount::where('type_code', '01')
            ->orderBy('account_code')
            ->get();
        $assetCategories = config('accounting.asset_categories');
        return view('assets.create', compact('parentAssets', 'assetCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'group_code' => 'required|string|size:2',
            'class_code' => 'required|string|size:2',
            'account_code' => 'required|string|size:4',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            ChartOfAccount::create([
                'type_code' => '01', // Assets type code
                'group_code' => $request->group_code,
                'class_code' => $request->class_code,
                'account_code' => $request->account_code,
                'name' => $request->name,
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'is_active' => $request->has('is_active'),
                'created_by' => Auth::id(),
            ]);
            DB::commit();
            return redirect()->route('assets.index')->with('success', 'Asset account created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error creating asset account: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(ChartOfAccount $asset)
    {
        // Return JSON for AJAX requests (used by modal)
        if (request()->ajax()) {
            return response()->json([
                'group_code' => $asset->group_code,
                'class_code' => $asset->class_code,
                'account_code' => $asset->account_code,
                'name' => $asset->name,
                'description' => $asset->description,
                'parent_id' => $asset->parent_id,
                'is_active' => $asset->is_active,
            ]);
        }

        // Fallback for non-AJAX (optional)
        $parentAssets = ChartOfAccount::where('type_code', '01')
            ->where('id', '!=', $asset->id)
            ->orderBy('account_code')
            ->get();
        return view('assets.edit', compact('asset', 'parentAssets'));
    }

    public function update(Request $request, ChartOfAccount $asset)
    {
        $request->validate([
            'group_code' => 'required|string|size:2',
            'class_code' => 'required|string|size:2',
            'account_code' => 'required|string|size:4',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $asset->update([
                'group_code' => $request->group_code,
                'class_code' => $request->class_code,
                'account_code' => $request->account_code,
                'name' => $request->name,
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'is_active' => $request->has('is_active'),
                'updated_by' => Auth::id(),
            ]);
            DB::commit();
            return redirect()->route('assets.index')->with('success', 'Asset account updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating asset account: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(ChartOfAccount $asset)
    {
        DB::beginTransaction();
        try {
            if ($asset->children()->exists()) {
                throw new \Exception('Cannot delete asset with child accounts.');
            }

            if ($asset->transactions()->exists()) {
                throw new \Exception('Cannot delete asset with associated transactions.');
            }

            $asset->delete();
            DB::commit();
            return redirect()->route('assets.index')->with('success', 'Asset account deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error deleting asset account: ' . $e->getMessage());
        }
    }

    /**
     * Display a summary report of assets.
     */
    public function report()
    {
        $assets = ChartOfAccount::where('type_code', '01')
            ->with(['children', 'parent'])
            ->orderBy('group_code')
            ->orderBy('class_code')
            ->orderBy('account_code')
            ->get();

        $assetCategories = config('accounting.asset_categories');
        $groups = config('accounting.groups.01');
        $classes = config('accounting.classes.01');

        // Group assets by category, group, class for reporting
        $report = $assets->groupBy(['group_code', 'class_code']);

        return view('assets.report', compact('report', 'assetCategories', 'groups', 'classes'));
    }
}
