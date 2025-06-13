<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetDetail;
use App\Models\AssetTransaction;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\Warehouse;
use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssetController extends Controller
{
    public function __construct()
    {
        // Add middleware for access control if needed
        $this->middleware('auth');
    }

    public function index()
    {
        $assets = Asset::with(['category', 'details', 'transactions', 'documents'])
            ->orderBy('code')
            ->get();

        $assetCategories = config('accounting.asset_categories');
        $assetGroups = config('accounting.asset_groups');

        return view('assets.index', compact('assets', 'assetCategories', 'assetGroups'));
    }

    public function create()
    {
        try {
            // Get asset categories from database
            $assetCategories = AssetCategory::where('is_active', true)
                ->orderBy('name')
                ->get();

            // Get accounts for dropdown
            $accounts = ChartOfAccount::where('type_code', '01')
                ->orderBy('account_code')
                ->get();

            // Get locations
            $locations = Warehouse::orderBy('name')->get();

            // Define conditions
            $conditions = [
                'new' => 'New',
                'good' => 'Good',
                'fair' => 'Fair',
                'poor' => 'Poor'
            ];

            // Define depreciation methods
            $depreciationMethods = [
                'straight_line' => 'Straight Line',
                'declining_balance' => 'Declining Balance',
                'sum_of_years' => 'Sum of Years'
            ];

            return view('assets.create', [
                'assetCategories' => $assetCategories,
                'accounts' => $accounts,
                'locations' => $locations,
                'conditions' => $conditions,
                'depreciationMethods' => $depreciationMethods
            ]);
        } catch (\Exception $e) {
            Log::error('Error in asset create:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('assets.index')
                ->with('error', 'Error loading asset creation form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:asset_categories,id',
                'description' => 'nullable|string',
                'purchase_date' => 'required|date',
                'purchase_price' => 'required|numeric|min:0',
                'serial_number' => 'nullable|string|max:255',
                'warranty_expiry' => 'nullable|date',
                'depreciation_method' => 'required|in:straight_line,declining_balance,sum_of_years,double_declining,units_of_production',
                'depreciation_rate' => 'required|numeric|min:0|max:100',
                'useful_life' => 'required|integer|min:1',
                'location' => 'required|string|max:255',
                'condition' => 'required|in:new,good,fair,poor,critical',
                'notes' => 'nullable|string',
            ]);

            // Get the asset category
            $category = AssetCategory::findOrFail($validated['category_id']);
            
            // Get the chart of account for this asset
            $account = ChartOfAccount::where('type_code', 'asset')
                ->where('account_code', 'like', $category->code . '%')
                ->first();

            if (!$account) {
                throw new \Exception('No matching chart of account found for this asset category.');
            }

            // Generate asset code
            $code = $this->generateAssetCode($category->code);

            // Create asset
            $asset = Asset::create([
                'chart_of_account_id' => $account->id,
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'code' => $code,
                'description' => $validated['description'],
                'purchase_date' => $validated['purchase_date'],
                'purchase_price' => $validated['purchase_price'],
                'status' => true,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            // Create asset details
            $asset->details()->create([
                'serial_number' => $validated['serial_number'] ?? null,
                'purchase_date' => $validated['purchase_date'],
                'purchase_price' => $validated['purchase_price'],
                'warranty_expiry' => $validated['warranty_expiry'] ?? null,
                'depreciation_method' => $validated['depreciation_method'],
                'depreciation_rate' => $validated['depreciation_rate'],
                'useful_life' => $validated['useful_life'],
                'location' => $validated['location'],
                'condition' => $validated['condition'],
                'notes' => $validated['notes'],
                'created_by' => auth()->id(),
                'updated_by' => auth()->id()
            ]);

            // Create initial journal entry for asset purchase
            $this->createAssetPurchaseJournalEntry($asset);

            DB::commit();
            return redirect()->route('assets.index')->with('success', 'Asset created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating asset: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error creating asset: ' . $e->getMessage());
        }
    }

    public function show(Asset $asset)
    {
        $asset->load(['category', 'details', 'transactions', 'documents', 'maintenanceRecords']);
        return view('assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        $asset->load('details');
        $assetCategories = config('accounting.asset_categories');
        $assetGroups = config('accounting.asset_groups');
        $accounts = ChartOfAccount::where('type_code', '01')
            ->orderBy('account_code')
            ->get();
        $locations = Warehouse::orderBy('name')->get();

        return view('assets.edit', compact('asset', 'assetCategories', 'assetGroups', 'accounts', 'locations'));
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:chart_of_accounts,id',
            'category_id' => 'required|exists:asset_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purchase_date' => 'required|date',
            'purchase_price' => 'required|numeric|min:0',
            'warranty_expiry' => 'nullable|date|after:purchase_date',
            'depreciation_method' => 'required|string',
            'depreciation_rate' => 'required|numeric|min:0|max:100',
            'useful_life' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'condition' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update asset
            $asset->update([
                'chart_of_account_id' => $validated['account_id'],
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'description' => $validated['description'],
                'updated_by' => auth()->id()
            ]);

            // Update asset details
            $asset->details()->update([
                'serial_number' => $validated['serial_number'] ?? null,
                'purchase_date' => $validated['purchase_date'],
                'purchase_price' => $validated['purchase_price'],
                'warranty_expiry' => $validated['warranty_expiry'] ?? null,
                'depreciation_method' => $validated['depreciation_method'],
                'depreciation_rate' => $validated['depreciation_rate'],
                'useful_life' => $validated['useful_life'],
                'location' => $validated['location'],
                'condition' => $validated['condition'],
                'notes' => $validated['notes'],
                'updated_by' => auth()->id()
            ]);

            DB::commit();
            return redirect()->route('assets.index')
                ->with('success', 'Asset updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating asset: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Asset $asset)
    {
        DB::beginTransaction();
        try {
            if ($asset->transactions()->exists()) {
                throw new \Exception('Cannot delete asset with associated transactions.');
            }

            $asset->details()->delete();
            $asset->documents()->delete();
            $asset->maintenanceRecords()->delete();
            $asset->delete();
            
            DB::commit();
            return redirect()->route('assets.index')
                ->with('success', 'Asset deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error deleting asset: ' . $e->getMessage());
        }
    }

    public function calculateDepreciation(Asset $asset)
    {
        DB::beginTransaction();
        try {
            $depreciationAmount = $asset->getDepreciationAmount();
            
            if ($depreciationAmount > 0) {
                $transaction = $asset->transactions()->create([
                    'type' => AssetTransaction::TYPE_DEPRECIATION,
                    'amount' => $depreciationAmount,
                    'date' => now(),
                    'description' => 'Monthly depreciation',
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id()
                ]);

                $transaction->createJournalEntry();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Depreciation calculated successfully',
                'amount' => $depreciationAmount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error calculating depreciation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function recordMaintenance(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'maintenance_type' => 'required|in:preventive,corrective,predictive,condition_based',
            'description' => 'required|string',
            'maintenance_date' => 'required|date',
            'next_maintenance_date' => 'required|date|after:maintenance_date',
            'cost' => 'required|numeric|min:0',
            'performed_by' => 'required|string'
        ]);

        DB::beginTransaction();
        try {
            $maintenance = $asset->maintenanceRecords()->create([
                'maintenance_type' => $validated['maintenance_type'],
                'description' => $validated['description'],
                'maintenance_date' => $validated['maintenance_date'],
                'next_maintenance_date' => $validated['next_maintenance_date'],
                'cost' => $validated['cost'],
                'performed_by' => $validated['performed_by'],
                'created_by' => auth()->id(),
                'updated_by' => auth()->id()
            ]);

            $transaction = $maintenance->createMaintenanceTransaction();
            $transaction->createJournalEntry();

            DB::commit();
            return redirect()->route('assets.show', $asset)
                ->with('success', 'Maintenance record created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error recording maintenance: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function dispose(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'disposal_date' => 'required|date',
            'disposal_amount' => 'required|numeric|min:0',
            'reason' => 'required|string'
        ]);

        DB::beginTransaction();
        try {
            $transaction = $asset->transactions()->create([
                'type' => AssetTransaction::TYPE_DISPOSAL,
                'amount' => $validated['disposal_amount'],
                'date' => $validated['disposal_date'],
                'description' => $validated['reason'],
                'created_by' => auth()->id(),
                'updated_by' => auth()->id()
            ]);

            $transaction->createJournalEntry();

            // Update asset status
            $asset->update(['status' => false]);

            DB::commit();
            return redirect()->route('assets.index')
                ->with('success', 'Asset disposed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error disposing asset: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function report()
    {
        $assets = Asset::with(['category', 'details', 'transactions'])
            ->orderBy('category_id')
            ->orderBy('code')
            ->get();

        $assetCategories = config('accounting.asset_categories');
        $assetGroups = config('accounting.asset_groups');

        // Group assets by category for reporting
        $report = $assets->groupBy('category_id');

        return view('assets.report', compact('report', 'assetCategories', 'assetGroups'));
    }

    private function generateAssetCode($categoryCode)
    {
        $baseCode = $categoryCode;
        $counter = 1;
        $code = $baseCode;
        
        while (Asset::where('code', $code)->exists()) {
            $code = $baseCode . '-' . $counter;
            $counter++;
        }
        
        return $code;
    }

    private function createAssetPurchaseJournalEntry($asset)
    {
        // Create journal entry
        $journalEntry = JournalEntry::create([
            'entry_date' => $asset->purchase_date,
            'reference_no' => 'ASSET-' . $asset->id,
            'description' => 'Asset Purchase: ' . $asset->name,
            'status' => 'posted',
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
            'posted_by' => auth()->id(),
            'posted_at' => now()
        ]);

        // Create journal entry items
        // Debit the asset account
        $journalEntry->items()->create([
            'chart_of_account_id' => $asset->chart_of_account_id,
            'debit' => $asset->purchase_price,
            'credit' => 0,
            'description' => 'Asset Purchase: ' . $asset->name,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id()
        ]);

        // Credit the bank account
        $bankAccount = ChartOfAccount::where('account_code', config('accounting.default_accounts.bank'))->first();
        if (!$bankAccount) {
            throw new \Exception('Default bank account not found. Please configure it in the accounting settings.');
        }

        $journalEntry->items()->create([
            'chart_of_account_id' => $bankAccount->id,
            'debit' => 0,
            'credit' => $asset->purchase_price,
            'description' => 'Asset Purchase: ' . $asset->name,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id()
        ]);

        // Create asset transaction
        AssetTransaction::create([
            'asset_id' => $asset->id,
            'type' => 'purchase',
            'amount' => $asset->purchase_price,
            'date' => $asset->purchase_date,
            'description' => 'Initial purchase of asset',
            'reference_type' => 'journal_entry',
            'reference_id' => $journalEntry->id,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id()
        ]);
    }
}
