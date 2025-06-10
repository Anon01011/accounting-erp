<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\AssetDetail;
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
        $assets = ChartOfAccount::where('type_code', '01')
            ->whereNull('parent_id')
            ->with(['children', 'assetDetails'])
            ->orderBy('account_code')
            ->get();

        $assetCategories = config('accounting.asset_categories');
        $assetGroups = config('accounting.asset_groups');

        return view('assets.index', compact('assets', 'assetCategories', 'assetGroups'));
    }

    public function create()
    {
        $assetCategories = config('accounting.asset_categories');
        $assetGroups = config('accounting.asset_groups');
        $parentAssets = ChartOfAccount::where('type_code', '01')
            ->whereNull('parent_id')
            ->orderBy('account_code')
            ->get();

        return view('assets.create', compact('assetCategories', 'assetGroups', 'parentAssets'));
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
            // Asset Details
            'serial_number' => 'nullable|string',
            'purchase_date' => 'required|date',
            'purchase_price' => 'required|numeric|min:0',
            'warranty_expiry' => 'nullable|date',
            'depreciation_method' => 'required|in:straight_line,declining_balance,sum_of_years',
            'depreciation_rate' => 'required|numeric|min:0|max:100',
            'useful_life' => 'required|integer|min:1',
            'location' => 'nullable|string',
            'condition' => 'required|in:new,good,fair,poor',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Create Chart of Account
            $account = ChartOfAccount::create([
                'type_code' => '01',
                'group_code' => $request->group_code,
                'class_code' => $request->class_code,
                'account_code' => $request->account_code,
                'name' => $request->name,
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'is_active' => $request->has('is_active'),
                'created_by' => Auth::id(),
            ]);

            // Create Asset Details
            $account->assetDetails()->create([
                'serial_number' => $request->serial_number,
                'purchase_date' => $request->purchase_date,
                'purchase_price' => $request->purchase_price,
                'warranty_expiry' => $request->warranty_expiry,
                'depreciation_method' => $request->depreciation_method,
                'depreciation_rate' => $request->depreciation_rate,
                'useful_life' => $request->useful_life,
                'location' => $request->location,
                'condition' => $request->condition,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('assets.index')->with('success', 'Asset created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error creating asset: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(ChartOfAccount $asset)
    {
        $asset->load('assetDetails');
        $assetCategories = config('accounting.asset_categories');
        $assetGroups = config('accounting.asset_groups');
        $parentAssets = ChartOfAccount::where('type_code', '01')
            ->where('id', '!=', $asset->id)
            ->whereNull('parent_id')
            ->orderBy('account_code')
            ->get();

        return view('assets.edit', compact('asset', 'assetCategories', 'assetGroups', 'parentAssets'));
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
            // Asset Details
            'serial_number' => 'nullable|string',
            'purchase_date' => 'required|date',
            'purchase_price' => 'required|numeric|min:0',
            'warranty_expiry' => 'nullable|date',
            'depreciation_method' => 'required|in:straight_line,declining_balance,sum_of_years',
            'depreciation_rate' => 'required|numeric|min:0|max:100',
            'useful_life' => 'required|integer|min:1',
            'location' => 'nullable|string',
            'condition' => 'required|in:new,good,fair,poor',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Update Chart of Account
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

            // Update Asset Details
            $asset->assetDetails()->update([
                'serial_number' => $request->serial_number,
                'purchase_date' => $request->purchase_date,
                'purchase_price' => $request->purchase_price,
                'warranty_expiry' => $request->warranty_expiry,
                'depreciation_method' => $request->depreciation_method,
                'depreciation_rate' => $request->depreciation_rate,
                'useful_life' => $request->useful_life,
                'location' => $request->location,
                'condition' => $request->condition,
                'notes' => $request->notes,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('assets.index')->with('success', 'Asset updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating asset: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(ChartOfAccount $asset)
    {
        DB::beginTransaction();
        try {
            if ($asset->children()->exists()) {
                throw new \Exception('Cannot delete asset with child accounts.');
            }

            if ($asset->assetTransactions()->exists()) {
                throw new \Exception('Cannot delete asset with associated transactions.');
            }

            $asset->assetDetails()->delete();
            $asset->delete();
            
            DB::commit();
            return redirect()->route('assets.index')->with('success', 'Asset deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error deleting asset: ' . $e->getMessage());
        }
    }

    /**
     * Display a summary report of assets.
     */
    public function report()
    {
        $assets = ChartOfAccount::where('type_code', '01')
            ->with(['children', 'parent', 'assetDetails'])
            ->orderBy('group_code')
            ->orderBy('class_code')
            ->orderBy('account_code')
            ->get();

        $assetCategories = config('accounting.asset_categories');
        $assetGroups = config('accounting.asset_groups');

        // Group assets by category, group, class for reporting
        $report = $assets->groupBy(['group_code', 'class_code']);

        return view('assets.report', compact('report', 'assetCategories', 'assetGroups'));
    }
}
