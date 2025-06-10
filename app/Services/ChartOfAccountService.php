<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\AccountType;
use App\Models\AccountGroup;
use App\Models\AccountClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChartOfAccountService
{
    public function getAccountTree()
    {
        return ChartOfAccount::with(['type', 'group', 'class', 'children'])
            ->whereNull('parent_id')
            ->orderBy('type_code')
            ->orderBy('group_code')
            ->orderBy('class_code')
            ->orderBy('account_code')
            ->get();
    }

    public function getAccountByCode($typeCode, $groupCode, $classCode, $accountCode)
    {
        return ChartOfAccount::where('type_code', $typeCode)
            ->where('group_code', $groupCode)
            ->where('class_code', $classCode)
            ->where('account_code', $accountCode)
            ->first();
    }

    public function createAccount(array $data)
    {
        DB::beginTransaction();

        try {
            // Generate account code if not provided
            if (!isset($data['account_code'])) {
                $data['account_code'] = ChartOfAccount::generateAccountCode(
                    $data['type_code'],
                    $data['group_code'],
                    $data['class_code']
                );
            }

            $account = ChartOfAccount::create($data);

            // Log the creation
            Log::info('Chart of Account created', [
                'account_id' => $account->id,
                'full_code' => $account->full_account_code,
                'created_by' => auth()->id()
            ]);

            DB::commit();
            return $account;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Chart of Account', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function updateAccount(ChartOfAccount $account, array $data)
    {
        DB::beginTransaction();

        try {
            $oldData = $account->toArray();
            $account->update($data);

            // Log the changes
            Log::info('Chart of Account updated', [
                'account_id' => $account->id,
                'old_data' => $oldData,
                'new_data' => $account->toArray(),
                'updated_by' => auth()->id()
            ]);

            DB::commit();
            return $account;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating Chart of Account', [
                'error' => $e->getMessage(),
                'account_id' => $account->id,
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function deleteAccount(ChartOfAccount $account)
    {
        if (!$account->canBeDeleted()) {
            throw new \Exception('Cannot delete account: It has children or transactions.');
        }

        DB::beginTransaction();

        try {
            $account->delete();

            // Log the deletion
            Log::info('Chart of Account deleted', [
                'account_id' => $account->id,
                'full_code' => $account->full_account_code,
                'deleted_by' => auth()->id()
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting Chart of Account', [
                'error' => $e->getMessage(),
                'account_id' => $account->id
            ]);
            throw $e;
        }
    }

    public function getAccountTypes()
    {
        return AccountType::active()->orderBy('code')->get();
    }

    public function getAccountGroups($typeCode)
    {
        return AccountGroup::active()
            ->where('type_code', $typeCode)
            ->orderBy('code')
            ->get();
    }

    public function getAccountClasses($typeCode, $groupCode)
    {
        return AccountClass::active()
            ->where('type_code', $typeCode)
            ->where('group_code', $groupCode)
            ->orderBy('code')
            ->get();
    }

    public function getParentAccounts($typeCode, $groupCode, $classCode)
    {
        return ChartOfAccount::where('type_code', $typeCode)
            ->where('group_code', $groupCode)
            ->where('class_code', $classCode)
            ->orderBy('account_code')
            ->get();
    }

    public function validateAccountHierarchy($typeCode, $groupCode, $classCode)
    {
        $type = AccountType::where('code', $typeCode)->first();
        if (!$type) {
            throw new \Exception('Invalid account type.');
        }

        $group = AccountGroup::where('type_code', $typeCode)
            ->where('code', $groupCode)
            ->first();
        if (!$group) {
            throw new \Exception('Invalid account group for the selected type.');
        }

        $class = AccountClass::where('type_code', $typeCode)
            ->where('group_code', $groupCode)
            ->where('code', $classCode)
            ->first();
        if (!$class) {
            throw new \Exception('Invalid account class for the selected group.');
        }

        return true;
    }
} 