<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Accounting\Models\ChartOfAccount;
use Illuminate\Support\Facades\DB;

class MasterAccountsSeeder extends Seeder
{
    // Account Levels
    const LEVEL_TYPE = 1;
    const LEVEL_GROUP = 2;
    const LEVEL_CLASS = 3;
    const LEVEL_ACCOUNT = 4;

    public function run()
    {
        DB::beginTransaction();
        try {
            // Create master account types
            foreach (config('accounting.account_types') as $typeCode => $typeName) {
                $this->createOrUpdateMasterAccount($typeCode, $typeName, null, self::LEVEL_TYPE);
            }

            // Create master account groups
            foreach (config('accounting.groups') as $typeCode => $groups) {
                foreach ($groups as $groupCode => $groupName) {
                    $this->createOrUpdateMasterGroup($typeCode, $groupCode, $groupName);
                }
            }

            // Create master account classes
            foreach (config('accounting.classes') as $typeCode => $groups) {
                foreach ($groups as $groupCode => $classes) {
                    foreach ($classes as $classCode => $className) {
                        $this->createOrUpdateMasterClass($typeCode, $groupCode, $classCode, $className);
                    }
                }
            }

            // Update existing accounts to link with master accounts
            $this->updateExistingAccounts();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function createOrUpdateMasterAccount($typeCode, $name, $description = null, $level = self::LEVEL_TYPE)
    {
        $account = ChartOfAccount::updateOrCreate(
            [
                'type_code' => $typeCode,
                'is_master' => true,
                'account_level' => $level
            ],
            [
                'name' => $name,
                'description' => $description,
                'is_active' => true,
                'group_code' => '00', // Default group code for type level
                'class_code' => '00', // Default class code for type level
                'account_code' => '0000' // Default account code for type level
            ]
        );

        return $account;
    }

    protected function createOrUpdateMasterGroup($typeCode, $groupCode, $name, $description = null)
    {
        $typeAccount = ChartOfAccount::where('type_code', $typeCode)
            ->where('is_master', true)
            ->where('account_level', self::LEVEL_TYPE)
            ->first();

        if (!$typeAccount) {
            throw new \Exception("Master account type not found: {$typeCode}");
        }

        return ChartOfAccount::updateOrCreate(
            [
                'type_code' => $typeCode,
                'group_code' => $groupCode,
                'is_master' => true,
                'account_level' => self::LEVEL_GROUP
            ],
            [
                'name' => $name,
                'description' => $description,
                'parent_id' => $typeAccount->id,
                'is_active' => true,
                'class_code' => '00', // Default class code for group level
                'account_code' => '0000' // Default account code for group level
            ]
        );
    }

    protected function createOrUpdateMasterClass($typeCode, $groupCode, $classCode, $name, $description = null)
    {
        $groupAccount = ChartOfAccount::where('type_code', $typeCode)
            ->where('group_code', $groupCode)
            ->where('is_master', true)
            ->where('account_level', self::LEVEL_GROUP)
            ->first();

        if (!$groupAccount) {
            throw new \Exception("Master account group not found: {$typeCode}.{$groupCode}");
        }

        return ChartOfAccount::updateOrCreate(
            [
                'type_code' => $typeCode,
                'group_code' => $groupCode,
                'class_code' => $classCode,
                'is_master' => true,
                'account_level' => self::LEVEL_CLASS
            ],
            [
                'name' => $name,
                'description' => $description,
                'parent_id' => $groupAccount->id,
                'is_active' => true,
                'account_code' => '0000' // Default account code for class level
            ]
        );
    }

    protected function updateExistingAccounts()
    {
        // Get all non-master accounts
        $accounts = ChartOfAccount::where('is_master', false)->get();

        foreach ($accounts as $account) {
            // Find the corresponding master class account
            $masterClass = ChartOfAccount::where('type_code', $account->type_code)
                ->where('group_code', $account->group_code)
                ->where('class_code', $account->class_code)
                ->where('is_master', true)
                ->where('account_level', self::LEVEL_CLASS)
                ->first();

            if ($masterClass) {
                $account->update([
                    'parent_id' => $masterClass->id,
                    'account_level' => self::LEVEL_ACCOUNT
                ]);
            }
        }
    }
} 