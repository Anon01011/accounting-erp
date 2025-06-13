<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetDetail;
use App\Models\AssetTransaction;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\Warehouse;
use App\Models\AssetCategory;
use App\Models\TaxGroup;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

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

            // Get tax groups
            $taxGroups = TaxGroup::where('is_active', true)->orderBy('name')->get();

            // Get suppliers
            $suppliers = Supplier::where('is_active', true)
                ->orderBy('name')
                ->get();

            // Define conditions
            $conditions = [
                'new' => 'New',
                'used' => 'Used',
                'refurbished' => 'Refurbished'
            ];

            // Define depreciation methods
            $depreciationMethods = [
                'straight_line' => 'Straight Line',
                'declining_balance' => 'Declining Balance',
                'sum_of_years' => 'Sum of Years Digits',
                'units_of_production' => 'Units of Production'
            ];

            return view('assets.create', [
                'assetCategories' => $assetCategories,
                'accounts' => $accounts,
                'locations' => $locations,
                'taxGroups' => $taxGroups,
                'suppliers' => $suppliers,
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
        Log::info('AssetController@store called', ['request' => $request->all()]);
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:asset_categories,id',
                'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
                'description' => 'nullable|string',
                'purchase_date' => 'required|date',
                'purchase_price' => 'required|numeric|min:0',
                'current_value' => 'required|numeric|min:0',
                'location' => 'required|string|max:255',
                'status' => 'required|in:active,inactive',
                'supplier_id' => 'required|exists:suppliers,id',
                'tax_group_id' => 'required|exists:tax_groups,id',
                'warranty_period' => 'nullable|integer|min:0',
                'warranty_expiry' => 'nullable|date',
                'depreciation_method' => 'required|in:straight_line,declining_balance,sum_of_years_digits,units_of_production',
                'depreciation_rate' => 'required|numeric|min:0|max:100',
                'useful_life' => 'required|integer|min:1',
                'notes' => 'nullable|string'
            ]);
            Log::info('Validation passed', ['validated' => $validated]);

            DB::beginTransaction();

            // Get category for code generation
            $category = AssetCategory::findOrFail($validated['category_id']);
            Log::info('Category found', ['category' => $category]);

            // Generate asset code
            $categoryPrefix = $category->code;
            Log::info('Category prefix: ' . $categoryPrefix);

            // Get the last asset code for this category
            $lastAsset = Asset::where('code', 'like', $categoryPrefix . '-%')
                ->orderBy('code', 'desc')
                ->first();

            $lastNumber = $lastAsset ? (int)substr($lastAsset->code, strrpos($lastAsset->code, '-') + 1) : 0;
            $newNumber = $lastNumber + 1;
            $assetCode = $categoryPrefix . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

            Log::info('Generated asset code', ['code' => $assetCode]);

            // Calculate warranty expiry if warranty period is provided
            $warrantyExpiry = null;
            if (!empty($validated['warranty_period'])) {
                $warrantyPeriod = (int)$validated['warranty_period'];
                $purchaseDate = Carbon::parse($validated['purchase_date']);
                $warrantyExpiry = $purchaseDate->copy()->addMonths($warrantyPeriod);
            } elseif (!empty($validated['warranty_expiry'])) {
                $warrantyExpiry = Carbon::parse($validated['warranty_expiry']);
            }

            // Create the asset
            $asset = Asset::create([
                'name' => $validated['name'],
                'code' => $assetCode,
                'category_id' => $validated['category_id'],
                'chart_of_account_id' => $validated['chart_of_account_id'],
                'description' => $validated['description'],
                'purchase_date' => $validated['purchase_date'],
                'purchase_price' => (float)$validated['purchase_price'],
                'current_value' => (float)$validated['current_value'],
                'location' => $validated['location'],
                'status' => $validated['status'] === 'active' ? 1 : 0,
                'supplier_id' => $validated['supplier_id'],
                'tax_group_id' => $validated['tax_group_id'],
                'warranty_expiry' => $warrantyExpiry,
                'depreciation_method' => $validated['depreciation_method'],
                'depreciation_rate' => (float)$validated['depreciation_rate'],
                'useful_life' => (int)$validated['useful_life'],
                'notes' => $validated['notes'],
                'is_active' => true
            ]);
            Log::info('Asset created', ['asset' => $asset]);

            // Create asset details
            $asset->details()->create([
                'purchase_date' => $validated['purchase_date'],
                'purchase_price' => (float)$validated['purchase_price'],
                'warranty_period' => (int)$validated['warranty_period'],
                'warranty_expiry' => $warrantyExpiry,
                'depreciation_method' => $validated['depreciation_method'],
                'depreciation_rate' => (float)$validated['depreciation_rate'],
                'useful_life' => (int)$validated['useful_life'],
                'depreciation_start_date' => $validated['purchase_date'],
                'depreciation_end_date' => Carbon::parse($validated['purchase_date'])->addYears((int)$validated['useful_life']),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id()
            ]);

            DB::commit();
            Log::info('Transaction committed');

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Asset created successfully',
                    'data' => $asset
                ]);
            }

            return redirect()->route('assets.index')
                ->with('success', 'Asset created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating asset', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating asset: ' . $e->getMessage()
                ], 500);
            }
            return back()->withInput()
                ->withErrors(['error' => 'Error creating asset: ' . $e->getMessage()]);
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
        $details = $asset->details->first();
        $assetCategories = \App\Models\AssetCategory::where('is_active', true)->orderBy('name')->get();
        $accounts = \App\Models\ChartOfAccount::where('type_code', '01')->orderBy('account_code')->get();
        $suppliers = \App\Models\Supplier::where('is_active', true)->orderBy('name')->get();
        $taxGroups = \App\Models\TaxGroup::where('is_active', true)->orderBy('name')->get();
        $locations = \App\Models\Warehouse::orderBy('name')->get();
        $conditions = [
            'new' => 'New',
            'used' => 'Used',
            'refurbished' => 'Refurbished'
        ];
        $depreciationMethods = [
            'straight_line' => 'Straight Line',
            'declining_balance' => 'Declining Balance',
            'sum_of_years' => 'Sum of Years Digits',
            'units_of_production' => 'Units of Production'
        ];
        return view('assets.edit', [
            'asset' => $asset,
            'details' => $details,
            'assetCategories' => $assetCategories,
            'accounts' => $accounts,
            'suppliers' => $suppliers,
            'taxGroups' => $taxGroups,
            'locations' => $locations,
            'conditions' => $conditions,
            'depreciationMethods' => $depreciationMethods
        ]);
    }

    public function update(Request $request, Asset $asset)
    {
        try {
            Log::info('AssetController@update called', ['request' => $request->all(), 'asset_id' => $asset->id]);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:assets,code,' . $asset->id,
                'category_id' => 'required|exists:asset_categories,id',
                'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
                'location' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'purchase_date' => 'required|date',
                'purchase_price' => 'required|numeric|min:0',
                'acquisition_cost' => 'required|numeric|min:0',
                'salvage_value' => 'required|numeric|min:0',
                'warehouse_id' => 'nullable|exists:warehouses,id',
                'details' => 'nullable|array',
                'details.serial_number' => 'nullable|string|max:100',
                'details.supplier' => 'nullable|string|max:255',
                'details.warranty_period' => 'nullable|integer|min:0',
                'details.warranty_expiry' => 'nullable|date',
                'details.depreciation_method' => 'nullable|string|in:straight_line,declining_balance,sum_of_years',
                'details.depreciation_rate' => 'nullable|numeric|min:0|max:100',
                'details.useful_life' => 'nullable|integer|min:1',
                'details.residual_value' => 'nullable|numeric|min:0',
                'details.condition' => 'nullable|string|in:new,good,fair,poor',
                'details.notes' => 'nullable|string'
            ]);

            Log::info('AssetController@update validated data', ['validated' => $validated, 'asset_id' => $asset->id]);

            DB::beginTransaction();

            try {
                Log::info('AssetController@update - updating asset', ['asset_id' => $asset->id]);

                // Prepare the update data
                $updateData = [
                    'name' => $validated['name'],
                    'code' => $validated['code'],
                    'category_id' => $validated['category_id'],
                    'chart_of_account_id' => $validated['chart_of_account_id'],
                    'location' => $validated['location'],
                    'description' => $validated['description'],
                    'purchase_date' => $validated['purchase_date'],
                    'purchase_price' => $validated['purchase_price'],
                    'acquisition_cost' => $validated['acquisition_cost'],
                    'salvage_value' => $validated['salvage_value'],
                ];

                // Only add warehouse_id if it exists in the validated data
                if (isset($validated['warehouse_id'])) {
                    $updateData['warehouse_id'] = $validated['warehouse_id'];
                }

                // Update the asset
                $asset->update($updateData);

                // Update or create asset details
                if (isset($validated['details'])) {
                    $asset->details()->updateOrCreate(
                        ['asset_id' => $asset->id],
                        $validated['details']
                    );
                }

                DB::commit();

                return redirect()->route('assets.show', $asset)
                    ->with('success', 'Asset updated successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error updating asset', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'request' => $request->all(),
                    'asset_id' => $asset->id
                ]);
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error in AssetController@update', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
                'asset_id' => $asset->id
            ]);
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update asset. Please try again.']);
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
            // Validate asset category settings
            if (!$asset->category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asset category not found'
                ], 400);
            }

            if (!$asset->category->depreciation_method) {
                return response()->json([
                    'success' => false,
                    'message' => 'Depreciation method not set for this asset category'
                ], 400);
            }

            if ($asset->category->default_useful_life <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Useful life must be greater than 0'
                ], 400);
            }

            // Check if asset is already fully depreciated
            if ($asset->getCurrentValue() <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asset is already fully depreciated'
                ], 400);
            }

            // Calculate depreciation amount
            $depreciationAmount = $asset->getDepreciationAmount();
            
            if ($depreciationAmount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No depreciation amount to record'
                ], 400);
            }

            // Create the depreciation transaction
            $transaction = $asset->transactions()->create([
                'type' => 'depreciation',
                'amount' => $depreciationAmount,
                'date' => now(),
                'description' => 'Monthly depreciation',
                'created_by' => auth()->id(),
                'updated_by' => auth()->id()
            ]);

            // Update asset's accumulated depreciation and current value
            $asset->update([
                'accumulated_depreciation' => $asset->accumulated_depreciation + $depreciationAmount,
                'current_value' => $asset->purchase_price - ($asset->accumulated_depreciation + $depreciationAmount)
            ]);

            // Try to create journal entry if accounting is configured
            try {
                if (config('accounting.default_accounts.depreciation_expense') && 
                    config('accounting.default_accounts.accumulated_depreciation')) {
                    $transaction->createJournalEntry();
                }
            } catch (\Exception $e) {
                // Log the error but don't fail the transaction
                Log::warning('Failed to create journal entry for depreciation: ' . $e->getMessage());
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Depreciation calculated successfully',
                'amount' => $depreciationAmount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error calculating depreciation: ' . $e->getMessage());
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
            'disposal_value' => 'required|numeric|min:0',
            'disposal_method' => 'required|string',
            'description' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $asset->update([
                'disposal_date' => $validated['disposal_date'],
                'disposal_value' => $validated['disposal_value'],
                'disposal_method' => $validated['disposal_method'],
                'status' => false,
                'updated_by' => auth()->id()
            ]);

            // Create disposal transaction
            $asset->transactions()->create([
                'type' => 'disposal',
                'transaction_type' => 'credit',
                'amount' => $validated['disposal_value'],
                'date' => $validated['disposal_date'],
                'description' => $validated['description'] ?? 'Asset disposal',
                'created_by' => auth()->id(),
                'updated_by' => auth()->id()
            ]);

            DB::commit();
            return redirect()->route('assets.show', $asset)
                ->with('success', 'Asset disposed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error disposing asset: ' . $e->getMessage());
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

    public function generateCode(AssetCategory $category)
    {
        try {
            \Log::info('Generating code for category: ' . $category->name);
            $code = $this->generateAssetCode($category);
            \Log::info('Generated code: ' . $code);
            return response()->json([
                'success' => true,
                'code' => $code
            ]);
        } catch (\Exception $e) {
            \Log::error('Error generating code: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generating code: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateAssetCode($category)
    {
        try {
            // Get the first three letters of the category name
            $prefix = strtoupper(substr($category->name, 0, 3));
            Log::info('Category prefix: ' . $prefix);
            
            // Find the last asset code with this prefix
            $lastAsset = Asset::where('code', 'like', $prefix . '-%')
                ->orderBy('code', 'desc')
                ->first();
            
            if ($lastAsset) {
                Log::info('Last asset found: ' . $lastAsset->code);
                // Extract the number and increment
                $lastNumber = (int) substr($lastAsset->code, strlen($prefix) + 1);
                $newNumber = $lastNumber + 1;
            } else {
                Log::info('No previous assets found with prefix: ' . $prefix);
                $newNumber = 1;
            }
            
            // Format the new code
            $newCode = $prefix . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
            Log::info('Generated new code: ' . $newCode);
            
            return $newCode;
        } catch (\Exception $e) {
            Log::error('Error generating asset code: ' . $e->getMessage());
            throw $e;
        }
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
