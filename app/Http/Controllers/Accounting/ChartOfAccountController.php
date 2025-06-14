<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Domains\Accounting\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;
use League\Csv\Reader;

class ChartOfAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Load initial 15 parent accounts with their relationships
        $accounts = ChartOfAccount::with(['parent', 'children'])
            ->whereNull('parent_id')  // Only get parent accounts
            ->orderBy('type_code')
            ->orderBy('group_code')
            ->orderBy('class_code')
            ->orderBy('account_code')
            ->take(15)
            ->get();

        $hasMore = ChartOfAccount::whereNull('parent_id')->count() > 15;

        return view('chart-of-accounts.index', compact('accounts', 'hasMore'));
    }

    public function loadMore(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = 15;

        $accounts = ChartOfAccount::with(['parent', 'children'])
            ->whereNull('parent_id')  // Only get parent accounts
            ->orderBy('type_code')
            ->orderBy('group_code')
            ->orderBy('class_code')
            ->orderBy('account_code')
            ->skip($offset)
            ->take($limit)
            ->get();

        $hasMore = ChartOfAccount::whereNull('parent_id')->count() > ($offset + $limit);

        return response()->json([
            'accounts' => $accounts,
            'hasMore' => $hasMore
        ]);
    }

    public function create()
    {
        $parentAccounts = ChartOfAccount::where('is_active', true)
            ->orderBy('type_code')
            ->orderBy('group_code')
            ->orderBy('class_code')
            ->orderBy('account_code')
            ->get();

        return view('chart-of-accounts.create', compact('parentAccounts'));
    }

    public function store(Request $request)
    {
        Log::info('Starting account creation process', ['request_data' => $request->all()]);

        $validated = $request->validate([
            'type_code' => 'required|string|max:3',
            'group_code' => 'required|string|max:8',
            'class_code' => 'required|string|max:2',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'is_active' => 'boolean',
        ]);

        Log::info('Validation passed', ['validated_data' => $validated]);

        DB::beginTransaction();
        try {
            // Generate account code if not provided
            if (!isset($validated['account_code'])) {
                Log::info('Generating account code');
                $validated['account_code'] = ChartOfAccount::generateAccountCode(
                    $validated['type_code'],
                    $validated['group_code'],
                    $validated['class_code']
                );
                Log::info('Generated account code', ['account_code' => $validated['account_code']]);
            }

            $validated['created_by'] = Auth::id();
            Log::info('Creating account with data', ['account_data' => $validated]);
            
            $account = ChartOfAccount::create($validated);
            Log::info('Account created successfully', ['account_id' => $account->id, 'account_code' => $account->account_code]);

            DB::commit();
            Log::info('Transaction committed successfully');
            
            return redirect()->route('chart-of-accounts.index')
                ->with('success', 'Account created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating account', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return redirect()->back()
                ->with('error', 'Error creating account: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(ChartOfAccount $account)
    {
        Log::info('Editing account', ['account_id' => $account->id, 'account_code' => $account->account_code]);

        $parentAccounts = ChartOfAccount::where('is_active', true)
            ->where('id', '!=', $account->id)
            ->orderBy('type_code')
            ->orderBy('group_code')
            ->orderBy('class_code')
            ->orderBy('account_code')
            ->get();

        // Load account types from config
        $accountTypes = config('accounting.account_types', []);
        Log::info('Loaded account types', ['account_types' => $accountTypes]);

        // Load account groups for the current type
        $accountGroups = [];
        if ($account->type_code) {
            $accountGroups = config('accounting.groups.' . $account->type_code, []);
            Log::info('Loaded account groups', ['type_code' => $account->type_code, 'account_groups' => $accountGroups]);
        }

        // Load account classes for the current type and group
        $accountClasses = [];
        if ($account->type_code && $account->group_code) {
            $accountClasses = config('accounting.classes.' . $account->type_code . '.' . $account->group_code, []);
            Log::info('Loaded account classes', ['type_code' => $account->type_code, 'group_code' => $account->group_code, 'account_classes' => $accountClasses]);
        }

        return view('chart-of-accounts.edit', compact(
            'account',
            'parentAccounts',
            'accountTypes',
            'accountGroups',
            'accountClasses'
        ));
    }

    public function update(Request $request, ChartOfAccount $account)
    {
        $validated = $request->validate([
            'type_code' => 'required|string|max:2',
            'group_code' => 'required|string|max:2',
            'class_code' => 'required|string|max:3',
            'account_code' => [
                'required',
                'string',
                'max:9',
                Rule::unique('chart_of_accounts')->ignore($account->id)
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $account->update($validated);

            DB::commit();

            return redirect()->route('chart-of-accounts.index')
                ->with('success', 'Account updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating account: ' . $e->getMessage());
            return back()->with('error', 'Failed to update account: ' . $e->getMessage());
        }
    }

    public function destroy(ChartOfAccount $account)
    {
        if ($account->children()->exists()) {
            return redirect()->route('chart-of-accounts.index')
                ->with('error', 'Cannot delete account with child accounts.');
        }

        if ($account->journalEntries()->exists()) {
            return redirect()->route('chart-of-accounts.index')
                ->with('error', 'Cannot delete account with associated journal entries.');
        }

        DB::beginTransaction();
        try {
            $account->delete();
            DB::commit();
            return redirect()->route('chart-of-accounts.index')
                ->with('success', 'Account deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('chart-of-accounts.index')
                ->with('error', 'Error deleting account: ' . $e->getMessage());
        }
    }

    // API endpoints for dynamic form updates
    public function getAccountGroups(Request $request)
    {
        try {
            Log::info('Fetching account groups', ['type_code' => $request->type_code]);
            
            if (!$request->type_code) {
                return response()->json(['error' => 'Type code is required'], 400);
            }
            
            $groups = config('accounting.groups.' . $request->type_code, []);
            Log::info('Account groups response', ['groups' => $groups]);
            
            if (empty($groups)) {
                return response()->json(['error' => 'No groups found for the selected type'], 404);
            }
            
            return response()->json($groups);
        } catch (\Exception $e) {
            Log::error('Error fetching account groups', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to fetch account groups: ' . $e->getMessage()], 500);
        }
    }

    public function getAccountClasses(Request $request)
    {
        try {
            Log::info('Fetching account classes', [
                'type_code' => $request->type_code,
                'group_code' => $request->group_code
            ]);
            
            if (!$request->type_code || !$request->group_code) {
                return response()->json(['error' => 'Type code and group code are required'], 400);
            }
            
            $classes = config('accounting.classes.' . $request->type_code . '.' . $request->group_code, []);
            Log::info('Account classes response', ['classes' => $classes]);
            
            if (empty($classes)) {
                return response()->json(['error' => 'No classes found for the selected type and group'], 404);
            }
            
            return response()->json($classes);
        } catch (\Exception $e) {
            Log::error('Error fetching account classes', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to fetch account classes: ' . $e->getMessage()], 500);
        }
    }

    public function getParentAccounts(Request $request)
    {
        $accounts = ChartOfAccount::where('type_code', $request->type_code)
            ->where('group_code', $request->group_code)
            ->where('class_code', $request->class_code)
            ->orderBy('account_code')
            ->get();

        return response()->json($accounts);
    }

    public function updateStatus(Request $request, ChartOfAccount $account)
    {
        try {
            DB::beginTransaction();

            // Update the account status
            $account->is_active = (bool)$request->is_active;
            $account->save();

            // If deactivating, also deactivate all child accounts
            $childrenUpdated = 0;
            if (!$request->is_active) {
                $childrenUpdated = $account->children()->update(['is_active' => false]);
            } else {
                // If activating, also activate all child accounts
                $childrenUpdated = $account->children()->update(['is_active' => true]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Account status updated successfully',
                'children_updated' => $childrenUpdated
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating account status', [
                'account_id' => $account->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update account status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import accounts from CSV file
     */
    public function import(Request $request)
    {
        try {
            if (!$request->hasFile('file')) {
                return response()->json(['message' => 'No file uploaded'], 400);
            }

            $file = $request->file('file');
            if ($file->getClientOriginalExtension() !== 'csv') {
                return response()->json(['message' => 'Only CSV files are allowed'], 400);
            }

            $csv = Reader::createFromPath($file->getPathname());
            $csv->setHeaderOffset(0);

            $records = $csv->getRecords();
            $imported = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($records as $record) {
                try {
                    // Validate required fields
                    $requiredFields = ['type_code', 'group_code', 'class_code', 'account_code', 'name'];
                    foreach ($requiredFields as $field) {
                        if (empty($record[$field])) {
                            throw new \Exception("Missing required field: {$field}");
                        }
                    }

                    // Create or update account
                    ChartOfAccount::updateOrCreate(
                        ['account_code' => $record['account_code']],
                        [
                            'type_code' => $record['type_code'],
                            'group_code' => $record['group_code'],
                            'class_code' => $record['class_code'],
                            'name' => $record['name'],
                            'description' => $record['description'] ?? null,
                            'is_active' => filter_var($record['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
                        ]
                    );

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($imported + 1) . ": " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Successfully imported {$imported} accounts.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', $errors);
            }

            return response()->json(['message' => $message], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import error: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export accounts to CSV file
     */
    public function export()
    {
        try {
            $accounts = ChartOfAccount::all();
            
            $csv = Writer::createFromString('');
            $csv->insertOne([
                'type_code',
                'group_code',
                'class_code',
                'account_code',
                'name',
                'description',
                'is_active',
                'parent_id'
            ]);

            foreach ($accounts as $account) {
                $csv->insertOne([
                    $account->type_code,
                    $account->group_code,
                    $account->class_code,
                    $account->account_code,
                    $account->name,
                    $account->description,
                    $account->is_active ? 'true' : 'false',
                    $account->parent_id
                ]);
            }

            $filename = 'chart_of_accounts_' . date('Y-m-d_His') . '.csv';
            
            return response($csv->toString(), 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return response()->json(['message' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Download sample template
     */
    public function template()
    {
        try {
            $csv = Writer::createFromString('');
            $csv->insertOne([
                'type_code',
                'group_code',
                'class_code',
                'account_code',
                'name',
                'description',
                'is_active',
                'parent_id'
            ]);

            // Add sample data
            $csv->insertOne([
                '01', // type_code
                '01', // group_code
                '01', // class_code
                '0001', // account_code
                'Sample Account', // name
                'Sample description', // description
                'true', // is_active
                '' // parent_id
            ]);

            $filename = 'chart_of_accounts_template.csv';
            
            return response($csv->toString(), 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

        } catch (\Exception $e) {
            Log::error('Template error: ' . $e->getMessage());
            return response()->json(['message' => 'Template download failed: ' . $e->getMessage()], 500);
        }
    }

    public function show(ChartOfAccount $account)
    {
        // Load relationships
        $account->load(['parent', 'children']);

        // Get related accounts (accounts with the same type, group, and class)
        $relatedAccounts = ChartOfAccount::where('type_code', $account->type_code)
            ->where('group_code', $account->group_code)
            ->where('class_code', $account->class_code)
            ->where('id', '!=', $account->id)
            ->whereHas('journalEntries') // only accounts used in journal entries
            ->orderBy('account_code')
            ->take(5)
            ->get();

        // Get recent journal entries for this account via items
        $recentJournalEntries = \App\Models\JournalEntry::whereHas('items', function($query) use ($account) {
            $query->where('chart_of_account_id', $account->id);
        })
        ->latest('entry_date')
        ->take(10)
        ->get();

        // Calculate stats using journal_entry_items
        $account->total_debits = \App\Models\JournalEntryItem::where('chart_of_account_id', $account->id)->sum('debit');
        $account->total_credits = \App\Models\JournalEntryItem::where('chart_of_account_id', $account->id)->sum('credit');
        $account->current_balance = $account->total_debits - $account->total_credits;

        return view('chart-of-accounts.show', compact('account', 'recentJournalEntries', 'relatedAccounts'));
    }
} 