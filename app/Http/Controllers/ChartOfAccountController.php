<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Services\ChartOfAccountService;
use App\Http\Requests\ChartOfAccountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ChartOfAccountController extends Controller
{
    protected $chartOfAccountService;

    public function __construct(ChartOfAccountService $chartOfAccountService)
    {
        $this->chartOfAccountService = $chartOfAccountService;
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accounts = ChartOfAccount::with('children')
            ->whereNull('parent_id')
            ->orderBy('type_code')
            ->orderBy('group_code')
            ->orderBy('class_code')
            ->orderBy('account_code')
            ->get();

        return view('chart-of-accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentAccounts = ChartOfAccount::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('chart-of-accounts.create', compact('parentAccounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_code' => 'required|string|max:2',
            'group_code' => 'required|string|max:2',
            'class_code' => 'required|string|max:2',
            'account_code' => 'required|string|max:4',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $account = ChartOfAccount::create($validated);
            DB::commit();
            return redirect()->route('chart-of-accounts.show', $account)
                ->with('success', 'Account created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating account: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ChartOfAccount $account)
    {
        // Load account with its relationships
        $account->load(['parent', 'children']);

        // Load account types from config
        $accountTypes = config('accounting.account_types', []);
        
        // Load account groups for the current type
        $accountGroups = [];
        if ($account->type_code) {
            $accountGroups = config('accounting.account_groups.' . $account->type_code, []);
        }
        
        // Load account classes for the current type and group
        $accountClasses = [];
        if ($account->type_code && $account->group_code) {
            $accountClasses = config('accounting.account_classes.' . $account->type_code . '.' . $account->group_code, []);
        }

        // Load recent journal entries for this account
        $recentJournalEntries = JournalEntry::with(['items' => function($query) use ($account) {
                $query->where('chart_of_account_id', $account->id);
            }])
            ->whereHas('items', function($query) use ($account) {
                $query->where('chart_of_account_id', $account->id);
            })
            ->orderBy('entry_date', 'desc')
            ->take(10)
            ->get();

        // Calculate account statistics
        $account->total_debits = $account->items()->sum('debit');
        $account->total_credits = $account->items()->sum('credit');
        $account->current_balance = $account->total_debits - $account->total_credits;

        return view('chart-of-accounts.show', compact(
            'account',
            'accountTypes',
            'accountGroups',
            'accountClasses',
            'recentJournalEntries'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChartOfAccount $account)
    {
        $parentAccounts = ChartOfAccount::where('is_active', true)
            ->where('id', '!=', $account->id)
            ->orderBy('name')
            ->get();

        return view('chart-of-accounts.edit', compact('account', 'parentAccounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChartOfAccount $account)
    {
        $validated = $request->validate([
            'type_code' => 'required|string|max:2',
            'group_code' => 'required|string|max:2',
            'class_code' => 'required|string|max:2',
            'account_code' => 'required|string|max:4',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $account->update($validated);
            DB::commit();
            return redirect()->route('chart-of-accounts.show', $account)
                ->with('success', 'Account updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating account: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChartOfAccount $account)
    {
        if ($account->children()->exists()) {
            return redirect()->route('chart-of-accounts.index')
                ->with('error', 'Cannot delete account with child accounts.');
        }

        if ($account->items()->exists()) {
            return redirect()->route('chart-of-accounts.index')
                ->with('error', 'Cannot delete account with journal entries.');
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

    public function getGroups(Request $request)
    {
        $groups = config('accounting.account_groups.' . $request->type_code);
        return response()->json($groups);
    }

    public function getClasses(Request $request)
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

    protected function validateAccountHierarchy($typeCode, $groupCode, $classCode)
    {
        // Validate type exists
        if (!array_key_exists($typeCode, config('accounting.account_types'))) {
            throw new \Exception('Invalid account type.');
        }

        // Validate group exists for type
        if (!array_key_exists($groupCode, config('accounting.account_groups.' . $typeCode))) {
            throw new \Exception('Invalid account group for selected type.');
        }

        // Validate class exists for group
        if (!array_key_exists($classCode, config('accounting.account_classes.' . $typeCode . '.' . $groupCode))) {
            throw new \Exception('Invalid account class for selected group.');
        }
    }

    protected function generateAccountCode($typeCode, $groupCode, $classCode)
    {
        // Get the last account number for this type/group/class combination
        $lastAccount = ChartOfAccount::where('type_code', $typeCode)
            ->where('group_code', $groupCode)
            ->where('class_code', $classCode)
            ->orderBy('account_code', 'desc')
            ->first();

        // Extract the sequence number from the last account code
        $sequence = 1;
        if ($lastAccount) {
            $parts = explode('.', $lastAccount->account_code);
            if (count($parts) === 4) {
                $sequence = (int)$parts[3] + 1;
            }
        }

        // Format: TT.GG.CC.NNNN
        return sprintf('%s.%s.%s.%04d', $typeCode, $groupCode, $classCode, $sequence);
    }

    // API endpoints for dynamic form updates
    public function getAccountGroups($typeCode)
    {
        $groups = config('accounting.account_groups.' . $typeCode);
        return response()->json($groups);
    }

    public function getAccountClasses($typeCode, $groupCode)
    {
        $classes = config('accounting.account_classes.' . $typeCode . '.' . $groupCode);
        return response()->json($classes);
    }
}
