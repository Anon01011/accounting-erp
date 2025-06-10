<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Domains\Accounting\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ChartOfAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Load all accounts with their relationships
        $accounts = ChartOfAccount::with(['parent', 'children'])
            ->whereNull('parent_id')
            ->orderBy('type_code')
            ->orderBy('group_code')
            ->orderBy('class_code')
            ->orderBy('account_code')
            ->get();

        return view('chart-of-accounts.index', compact('accounts'));
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
        $validated = $request->validate([
            'type_code' => 'required|string|max:2',
            'group_code' => 'required|string|max:2',
            'class_code' => 'required|string|max:2',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Generate account code if not provided
            if (!isset($validated['account_code'])) {
                $validated['account_code'] = ChartOfAccount::generateAccountCode(
                    $validated['type_code'],
                    $validated['group_code'],
                    $validated['class_code']
                );
            }

            $validated['created_by'] = Auth::id();
            $account = ChartOfAccount::create($validated);

            DB::commit();
            return redirect()->route('chart-of-accounts.index')
                ->with('success', 'Account created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating account: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(ChartOfAccount $account)
    {
        $parentAccounts = ChartOfAccount::where('is_active', true)
            ->where('id', '!=', $account->id)
            ->orderBy('type_code')
            ->orderBy('group_code')
            ->orderBy('class_code')
            ->orderBy('account_code')
            ->get();

        return view('chart-of-accounts.edit', compact('account', 'parentAccounts'));
    }

    public function update(Request $request, ChartOfAccount $account)
    {
        // If this is a status update request
        if ($request->has('is_active')) {
            try {
                DB::beginTransaction();

                // Update the account status
                $account->update([
                    'is_active' => (bool)$request->is_active
                ]);

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
                    'children_updated' => $childrenUpdated ?? 0
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error updating account status: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update account status',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        // For full account updates
        $validated = $request->validate([
            'type_code' => 'required|exists:account_types,code',
            'group_code' => 'required|exists:account_groups,code',
            'class_code' => 'required|exists:account_classes,code',
            'account_code' => [
                'required',
                'string',
                'max:20',
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
            \Log::error('Error updating account: ' . $e->getMessage());
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
        $groups = config('accounting.account_groups.' . $request->type_code);
        return response()->json($groups);
    }

    public function getAccountClasses(Request $request)
    {
        $classes = config('accounting.account_classes.' . $request->type_code . '.' . $request->group_code);
        return response()->json($classes);
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
            \Log::error('Error updating account status', [
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
} 