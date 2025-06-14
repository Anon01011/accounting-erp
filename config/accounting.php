<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Account Types
    |--------------------------------------------------------------------------
    |
    | Define the account types in the chart of accounts.
    | Format: 'code' => 'name'
    |
    */
    'account_types' => [
        '01' => 'Assets',
        '02' => 'Liabilities',
        '03' => 'Capital',
        '04' => 'Income',
        '05' => 'Expense',
    ],

    /*
    |--------------------------------------------------------------------------
    | Account Groups
    |--------------------------------------------------------------------------
    |
    | Define the account groups for each type.
    | Format: 'type_code' => ['group_code' => 'name']
    |
    */
    'groups' => [
        '01' => [ // Assets
            '10' => 'Fixed Assets',
            '11' => 'Current Assets',
            '12' => 'Cash Accounts',
            '13' => 'Bank Accounts',
            '14' => 'Stock In Hand',
        ],
        '02' => [ // Liabilities
            '20' => 'Current Liabilities',
            '21' => 'Provisions',
            '22' => 'Long Term Loan',
        ],
        '03' => [ // Capital
            '30' => 'Equity',
        ],
        '04' => [ // Income
            '40' => 'Direct Income',
            '41' => 'Indirect Income',
        ],
        '05' => [ // Expense
            '50' => 'Direct Expense',
            '51' => 'Indirect Expense',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Account Classes
    |--------------------------------------------------------------------------
    |
    | Define the account classes for each group.
    | Format: 'type_code.group_code' => ['class_code' => 'name']
    |
    */
    'classes' => [
        '01' => [ // Assets
            '10' => [ // Fixed Assets
                '10' => 'Property, Plant and Equipment',
                '20' => 'Accumulated Depreciation',
                '30' => 'Right of use assets',
                '40' => 'Accumulated Dep ROU',
            ],
            '11' => [ // Current Assets
                '10' => 'Prepayments A/c',
                '20' => 'Staff Advances',
                '30' => 'Accounts Receivable',
                '40' => 'Other Current Asset',
                '50' => 'Legal Court Case',
                '60' => 'FMCG Customers',
            ],
            '12' => [ // Cash Accounts
                '10' => 'Cash and Cash Equivalents',
            ],
            '13' => [ // Bank Accounts
                '10' => 'Bank Accounts',
            ],
            '14' => [ // Stock In Hand
                '10' => 'Inventory',
            ],
        ],
        '02' => [ // Liabilities
            '20' => [ // Current Liabilities
                '10' => 'Accounts Payable',
                '20' => 'PDC Issued',
                '30' => 'Other Payable',
            ],
            '21' => [ // Provisions
                '10' => 'Provisions',
            ],
            '22' => [ // Long Term Loan
                '10' => 'Long Term Loan',
            ],
        ],
        '03' => [ // Capital
            '30' => [ // Equity
                '10' => 'Equity',
            ],
        ],
        '04' => [ // Income
            '40' => [ // Direct Income
                '10' => 'Sales Income',
            ],
            '41' => [ // Indirect Income
                '20' => 'Other Income',
            ],
        ],
        '05' => [ // Expense
            '50' => [ // Direct Expense
                '10' => 'Cost of Sales',
                '20' => 'Inventory Adjustment Account',
            ],
            '51' => [ // Indirect Expense
                '10' => 'HR Related Expenses',
                '20' => 'Administrative Expenses',
                '30' => 'Selling & Distribution Expenses',
                '40' => 'Finance Expenses',
                '50' => 'Vehicle Expenses',
                '60' => 'Depreciation',
                '70' => 'Rent Expenses',
                '80' => 'Profit & Loss A/c',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Asset Categories
    |--------------------------------------------------------------------------
    |
    | Define the categories for assets.
    |
    */
    'asset_categories' => [
        '01' => 'Current Assets',
        '02' => 'Fixed Assets',
        '03' => 'Intangible Assets',
        '04' => 'Investments',
        '05' => 'Other Assets',
        '06' => 'Prepaid Expenses',
        '07' => 'Accounts Receivable',
        '08' => 'Cash and Cash Equivalents',
        '09' => 'Inventory',
        '10' => 'Bank Accounts'
    ],

    /*
    |--------------------------------------------------------------------------
    | Asset Groups
    |--------------------------------------------------------------------------
    |
    | Define the groups for assets.
    |
    */
    'asset_groups' => [
        '01' => [ // Current Assets
            '01' => 'Cash and Cash Equivalents',
            '02' => 'Accounts Receivable',
            '03' => 'Inventory',
            '04' => 'Prepaid Expenses',
            '05' => 'Other Current Assets'
        ],
        '02' => [ // Fixed Assets
            '01' => 'Land',
            '02' => 'Buildings',
            '03' => 'Equipment',
            '04' => 'Vehicles',
            '05' => 'Furniture and Fixtures',
            '06' => 'Computer Equipment',
            '07' => 'Accumulated Depreciation'
        ],
        '03' => [ // Intangible Assets
            '01' => 'Patents',
            '02' => 'Trademarks',
            '03' => 'Copyrights',
            '04' => 'Goodwill',
            '05' => 'Software'
        ],
        '04' => [ // Investments
            '01' => 'Short-term Investments',
            '02' => 'Long-term Investments',
            '03' => 'Stocks',
            '04' => 'Bonds'
        ],
        '05' => [ // Other Assets
            '01' => 'Deferred Tax Assets',
            '02' => 'Security Deposits',
            '03' => 'Advances to Employees',
            '04' => 'Other Non-current Assets'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Account Number Format
    |--------------------------------------------------------------------------
    |
    | Define the format for account numbers.
    | Format: type_code.group_code.class_code.account_code
    | Example: 01.01.01.0001
    |
    */
    'account_number_format' => 'TT.GG.CC.NNNN', // Type.Group.Class.Sequence

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | The default currency code for the application.
    |
    */
    'default_currency' => 'QAR',

    /*
    |--------------------------------------------------------------------------
    | Fiscal Year
    |--------------------------------------------------------------------------
    |
    | The fiscal year settings for the system.
    |
    */
    'fiscal_year' => [
        'start_month' => 1, // January
        'end_month' => 12,  // December
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Accounts
    |--------------------------------------------------------------------------
    |
    | Define default Chart of Account IDs for various transactions.
    |
    */
    'default_accounts' => [
        'bank' => '01.12.10.0001', // Example: Cash and Cash Equivalents account
        'depreciation_expense' => '05.51.60.0001', // Example: Depreciation Expense account
        'maintenance_expense' => '05.51.99.0001', // Placeholder: Please update with your actual Maintenance Expense account ID
        'gain_on_disposal' => '04.41.99.0001', // Placeholder: Please update with your actual Gain on Disposal account ID
        'loss_on_disposal' => '05.51.98.0001', // Placeholder: Please update with your actual Loss on Disposal account ID
    ],
];