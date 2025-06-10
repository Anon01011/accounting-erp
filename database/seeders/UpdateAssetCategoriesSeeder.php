<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\DB;

class UpdateAssetCategoriesSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();
        try {
            // Get all asset accounts
            $assetAccounts = ChartOfAccount::where('type_code', '01')->get();

            foreach ($assetAccounts as $account) {
                // Determine new group and class codes based on account name
                $groupCode = $this->getGroupCode($account->name);
                $classCode = $this->getClassCode($account->name);

                // Generate new account code
                $newAccountCode = $this->generateAccountCode($groupCode, $classCode);

                // Update the account
                $account->update([
                    'group_code' => $groupCode,
                    'class_code' => $classCode,
                    'account_code' => $newAccountCode
                ]);
            }

            DB::commit();
            $this->command->info('Asset categories updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error updating asset categories: ' . $e->getMessage());
        }
    }

    private function getGroupCode($accountName)
    {
        $name = strtolower($accountName);
        
        if (str_contains($name, 'current asset') || str_contains($name, 'cash') || str_contains($name, 'bank')) {
            return '01';
        } elseif (str_contains($name, 'fixed asset') || str_contains($name, 'equipment') || str_contains($name, 'building')) {
            return '02';
        } elseif (str_contains($name, 'intangible asset') || str_contains($name, 'patent') || str_contains($name, 'copyright')) {
            return '03';
        } elseif (str_contains($name, 'investment') || str_contains($name, 'stock') || str_contains($name, 'bond')) {
            return '04';
        } else {
            return '05'; // Other Assets
        }
    }

    private function getClassCode($accountName)
    {
        $name = strtolower($accountName);
        
        if (str_contains($name, 'cash') || str_contains($name, 'bank')) {
            return '01';
        } elseif (str_contains($name, 'receivable') || str_contains($name, 'inventory')) {
            return '02';
        } elseif (str_contains($name, 'prepaid') || str_contains($name, 'advance')) {
            return '03';
        } elseif (str_contains($name, 'equipment') || str_contains($name, 'vehicle')) {
            return '04';
        } elseif (str_contains($name, 'building') || str_contains($name, 'land')) {
            return '05';
        } elseif (str_contains($name, 'patent') || str_contains($name, 'copyright')) {
            return '06';
        } elseif (str_contains($name, 'goodwill') || str_contains($name, 'trademark')) {
            return '07';
        } elseif (str_contains($name, 'stock') || str_contains($name, 'bond')) {
            return '08';
        } else {
            return '09'; // Other
        }
    }

    private function generateAccountCode($groupCode, $classCode)
    {
        // Get the last account number for this group and class
        $lastAccount = ChartOfAccount::where('type_code', '01')
            ->where('group_code', $groupCode)
            ->where('class_code', $classCode)
            ->orderBy('account_code', 'desc')
            ->first();

        // Generate new account number
        $newNumber = $lastAccount ? intval(substr($lastAccount->account_code, -4)) + 1 : 1;
        
        // Format: 01 (type) + group (2) + class (2) + number (4)
        return sprintf('%02d%02d%02d%04d', 1, $groupCode, $classCode, $newNumber);
    }
} 