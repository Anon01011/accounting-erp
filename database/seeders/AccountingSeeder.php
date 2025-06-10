<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccountType;
use App\Models\AccountGroup;
use App\Models\AccountClass;
use Illuminate\Support\Facades\DB;

class AccountingSeeder extends Seeder
{
    protected $types = [
        '1' => 'Assets',
        '2' => 'Liabilities',
        '3' => 'Equity',
        '4' => 'Revenue',
        '5' => 'Expenses',
    ];

    protected $groups = [
        '1' => [ // Assets
            '11' => 'Current Assets',
            '12' => 'Fixed Assets',
            '13' => 'Other Assets',
        ],
        '2' => [ // Liabilities
            '21' => 'Current Liabilities',
            '22' => 'Long-term Liabilities',
        ],
        '3' => [ // Equity
            '31' => 'Capital',
            '32' => 'Retained Earnings',
        ],
        '4' => [ // Revenue
            '41' => 'Operating Revenue',
            '42' => 'Other Revenue',
        ],
        '5' => [ // Expenses
            '51' => 'Operating Expenses',
            '52' => 'Financial Expenses',
            '53' => 'Other Expenses',
        ],
    ];

    protected $classes = [
        '1' => [ // Assets
            '11' => [ // Current Assets
                '111' => 'Cash and Cash Equivalents',
                '112' => 'Accounts Receivable',
                '113' => 'Inventory',
                '114' => 'Prepaid Expenses',
            ],
            '12' => [ // Fixed Assets
                '121' => 'Land',
                '122' => 'Buildings',
                '123' => 'Equipment',
                '124' => 'Vehicles',
            ],
            '13' => [ // Other Assets
                '131' => 'Intangible Assets',
                '132' => 'Investments',
            ],
        ],
        '2' => [ // Liabilities
            '21' => [ // Current Liabilities
                '211' => 'Accounts Payable',
                '212' => 'Short-term Loans',
                '213' => 'Accrued Expenses',
            ],
            '22' => [ // Long-term Liabilities
                '221' => 'Long-term Loans',
                '222' => 'Bonds Payable',
            ],
        ],
        '3' => [ // Equity
            '31' => [ // Capital
                '311' => 'Common Stock',
                '312' => 'Preferred Stock',
            ],
            '32' => [ // Retained Earnings
                '321' => 'Retained Earnings',
                '322' => 'Dividends',
            ],
        ],
        '4' => [ // Revenue
            '41' => [ // Operating Revenue
                '411' => 'Sales Revenue',
                '412' => 'Service Revenue',
            ],
            '42' => [ // Other Revenue
                '421' => 'Interest Income',
                '422' => 'Gain on Sale',
            ],
        ],
        '5' => [ // Expenses
            '51' => [ // Operating Expenses
                '511' => 'Cost of Goods Sold',
                '512' => 'Salaries and Wages',
                '513' => 'Rent Expense',
                '514' => 'Utilities Expense',
            ],
            '52' => [ // Financial Expenses
                '521' => 'Interest Expense',
                '522' => 'Bank Charges',
            ],
            '53' => [ // Other Expenses
                '531' => 'Depreciation Expense',
                '532' => 'Amortization Expense',
            ],
        ],
    ];

    public function run()
    {
        DB::beginTransaction();

        try {
            // Seed Account Types
            foreach ($this->types as $code => $name) {
                AccountType::create([
                    'code' => $code,
                    'name' => $name,
                    'is_active' => true
                ]);
            }

            // Seed Account Groups
            foreach ($this->groups as $typeCode => $groups) {
                foreach ($groups as $code => $name) {
                    AccountGroup::create([
                        'type_code' => $typeCode,
                        'code' => $code,
                        'name' => $name,
                        'is_active' => true
                    ]);
                }
            }

            // Seed Account Classes
            foreach ($this->classes as $typeCode => $groups) {
                foreach ($groups as $groupCode => $classes) {
                    foreach ($classes as $code => $name) {
                        AccountClass::create([
                            'type_code' => $typeCode,
                            'group_code' => $groupCode,
                            'code' => $code,
                            'name' => $name,
                            'is_active' => true
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
} 