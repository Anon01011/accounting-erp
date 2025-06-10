<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChartOfAccountsSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();
        try {
            // Assets
            $this->createAssetAccounts();
            
            // Liabilities
            $this->createLiabilityAccounts();
            
            // Capital
            $this->createCapitalAccounts();
            
            // Income
            $this->createIncomeAccounts();
            
            // Expense
            $this->createExpenseAccounts();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function createAssetAccounts()
    {
        // Fixed Assets
        $fixedAssets = $this->createAccount('01', '10', '10', '11101', 'Property, Plant and Equipment');
        $this->createChildAccounts($fixedAssets, [
            ['111010001', 'Software'],
            ['111010002', 'Machinery & Equipment'],
            ['111010003', 'Leasehold Development'],
            ['111010004', 'Computer and Accessories'],
            ['111010005', 'Furniture and fixtures'],
            ['111010006', 'Motor Vehicle'],
            ['111010007', 'LC Finance'],
        ]);

        $accumulatedDep = $this->createAccount('01', '10', '20', '11102', 'Accumulated Depreciation');
        $this->createChildAccounts($accumulatedDep, [
            ['111020001', 'Accumulated Dep-Software'],
            ['111020002', 'Accumulated Dep-Machinery & Equipment'],
            ['111020003', 'Accumulated Dep-Furniture and fixtures'],
            ['111020004', 'Accumulated Dep-Leasehold Development'],
            ['111020005', 'Accumulated Dep-Computer & Accessories'],
            ['111020006', 'Accumulated Dep-Motor Vehicle'],
            ['111020007', 'Accumulated Dep-Rou Motor vehicles'],
        ]);

        $rouAssets = $this->createAccount('01', '10', '30', '11103', 'Right of use assets');
        $this->createChildAccounts($rouAssets, [
            ['111030001', 'ROU Motor Vehicle'],
            ['111030002', 'ROU Warehouse'],
        ]);

        $accumulatedRou = $this->createAccount('01', '10', '40', '11104', 'Accumulated Dep ROU');
        $this->createChildAccounts($accumulatedRou, [
            ['111040001', 'Accumulated Dep-Rou Motor vehicles'],
            ['111040002', 'Accumulated Dep ROU Warehouse'],
        ]);

        // Current Assets
        $prepayments = $this->createAccount('01', '11', '10', '10101', 'Prepayments A/c');
        $this->createChildAccounts($prepayments, [
            ['101010001', 'Prepayments'],
            ['101010002', 'Prepaid Medical Insurance'],
            ['101010003', 'Retension Charge'],
            ['101010004', 'Prepaid Registration Fees'],
            ['101010005', 'Prepaid Lawyer Fees'],
            ['101010006', 'Prepaid Marketing Expenses'],
            ['101010007', 'Prepaid Interest Expenses'],
            ['101010008', 'Prepaid Interest Exp Al Jazeera'],
            ['101010009', 'Prepaid Exp for Economic Zone'],
            ['101010010', 'Prepaid LC Renewal Fee'],
        ]);

        $staffAdvances = $this->createAccount('01', '11', '20', '10102', 'Staff Advances');
        $this->createChildAccounts($staffAdvances, [
            ['101020001', 'Janna Advance A/c'],
            ['101020002', 'Sashidharan Salary Advance'],
            ['101020003', 'Angy Salary Advance'],
        ]);

        $receivables = $this->createAccount('01', '11', '30', '10103', 'Accounts Receivable');
        $this->createChildAccounts($receivables, [
            ['101030001', 'ADVANCED ENGINEERING MAINTENANCE COMPANY(BAM)'],
            ['101030002', 'ADVANCED FUTURE TECHNOLOGY'],
            ['101030003', 'AFCO W.L.L.'],
        ]);

        $otherAssets = $this->createAccount('01', '11', '40', '10104', 'Other Current Asset');
        $this->createChildAccounts($otherAssets, [
            ['101040001', 'PDC RECEIVED'],
            ['101040002', 'Areeba'],
            ['101040003', 'Security Deposit Fuel Tanks'],
            ['101040004', 'Eren Rent Security Deposit'],
            ['101040005', 'FMCG Suspense'],
            ['101040006', 'Doha Modern (Old Warehouse)'],
            ['101040007', 'Awqaf Guarantee'],
            ['101040008', 'DFRP Lite N Appetite'],
            ['101040009', 'DFRP Platinum'],
            ['101040010', 'Eco Flooring Customer'],
            ['101040011', 'Advance to Supplier'],
            ['101040012', 'Abdul Aziz Villa'],
            ['101040013', 'Charcoalite Trading'],
        ]);

        $legalCases = $this->createAccount('01', '11', '50', '10105', 'Legal Court Case');
        $this->createChildAccounts($legalCases, [
            ['101050001', 'Legal Case -Marina Trading & Contracting Co.W.L.L'],
            ['101050002', 'Legal Case-FastWay Trading & Contracting'],
            ['101050003', 'Legal Case-Polygon Trading & Contracting'],
            ['101050004', 'Legal Case-Alliance Partner Trading and Contracting'],
            ['101050005', 'LEGAL CASE AETERNUM CONTRACTING'],
            ['101050006', 'Legal Case - Inshah Contracting'],
        ]);

        $fmcgCustomers = $this->createAccount('01', '11', '60', '10106', 'FMCG Customers');
        $this->createChildAccounts($fmcgCustomers, [
            ['101060001', 'KROM Group'],
            ['101060002', 'PANDA HYPERMARKET'],
            ['101060003', 'Samples'],
            ['101060233', 'Salwa Family Shopping Complex'],
        ]);

        // Cash Accounts
        $cashAccounts = $this->createAccount('01', '12', '10', '12101', 'Cash and Cash Equivalents');
        $this->createChildAccounts($cashAccounts, [
            ['121010001', 'Cash In hand'],
            ['121010002', 'Petty Cash-Mep'],
            ['121010003', 'Petty Cash-Fmcg'],
            ['121010004', 'Petty Cash-Warehouse'],
        ]);

        // Bank Accounts
        $bankAccounts = $this->createAccount('01', '13', '10', '13101', 'Bank Accounts');
        $this->createChildAccounts($bankAccounts, [
            ['131010001', 'QIIB-1111-071786-001'],
            ['131010002', 'QIIB-1111-071786-002'],
        ]);

        // Stock In Hand
        $inventory = $this->createAccount('01', '14', '10', '14101', 'Inventory');
        $this->createChildAccounts($inventory, [
            ['141010001', 'Inventory'],
            ['141010002', 'Pending Delivery Note'],
            ['141010003', 'FMCG Inventory Account'],
        ]);
    }

    private function createLiabilityAccounts()
    {
        // Current Liabilities
        $payables = $this->createAccount('02', '20', '10', '20101', 'Accounts Payable');
        $this->createChildAccounts($payables, [
            ['201010001', 'FERPLAST Srl'],
            ['201010002', 'INKA DIS TICARET A.S.'],
        ]);

        $pdcIssued = $this->createAccount('02', '20', '20', '20102', 'PDC Issued');
        $this->createChildAccounts($pdcIssued, [
            ['201020001', 'PDC Issued'],
        ]);

        $otherPayable = $this->createAccount('02', '20', '30', '20103', 'Other Payable');
        $this->createChildAccounts($otherPayable, [
            ['201030001', 'Due to Hassan GM-Personal Drawings'],
            ['201030002', 'Freight Expense Payable A/C'],
            ['201030003', 'Due to - Lite N Appetite'],
            ['201030004', 'LC Charge Payable A/c'],
            ['201030005', 'Hassan Credit Card'],
        ]);

        // Provisions
        $provisions = $this->createAccount('02', '21', '10', '21101', 'Provisions');
        $this->createChildAccounts($provisions, [
            ['211010001', 'Accrued Expenses'],
            ['211010002', 'Accrued Payroll'],
            ['211010003', 'Provision for Leave Salary'],
            ['211010004', 'Almaha Insurance'],
            ['211010005', 'Provisions for EOS'],
            ['211010006', 'Provisions for Audit Fees'],
            ['211010007', 'Provision for Inventory write off'],
            ['211010008', 'Provision for Bad Debts'],
            ['211010009', 'Provision for Reserve Exchange gain or loss'],
        ]);

        // Long Term Loan
        $longTermLoan = $this->createAccount('02', '22', '10', '22101', 'Long Term Loan');
        $this->createChildAccounts($longTermLoan, [
            ['221010001', 'Lease Liability Vehicle'],
            ['221010002', 'Lease Liability Warehouse'],
        ]);
    }

    private function createCapitalAccounts()
    {
        $equity = $this->createAccount('03', '30', '10', '30101', 'Equity');
        $this->createChildAccounts($equity, [
            ['301010001', 'Share Capital'],
            ['301010002', 'Legal Reserve'],
            ['301010003', 'Retained Earnings'],
        ]);
    }

    private function createIncomeAccounts()
    {
        // Direct Income
        $salesIncome = $this->createAccount('04', '40', '10', '40101', 'Sales Income');
        $this->createChildAccounts($salesIncome, [
            ['401010001', 'Sales'],
            ['401010002', 'Sales Return'],
        ]);

        // Indirect Income
        $otherIncome = $this->createAccount('04', '41', '20', '40202', 'Other Income');
        $this->createChildAccounts($otherIncome, [
            ['402020001', 'Discount Received'],
            ['402020002', 'Exchange Rate Gain or Loss'],
            ['402020003', 'Other Income'],
            ['402020004', 'Exchange Rate Gain or loss-HO'],
        ]);
    }

    private function createExpenseAccounts()
    {
        // Direct Expense
        $costOfSales = $this->createAccount('05', '50', '10', '50101', 'Cost of Sales');
        $this->createChildAccounts($costOfSales, [
            ['501010001', 'Material Cost'],
            ['501010002', 'Fixed Rebate'],
        ]);

        $inventoryAdjustment = $this->createAccount('05', '50', '20', '50201', 'Inventory Adjustment Account');
        $this->createChildAccounts($inventoryAdjustment, [
            ['502010001', 'Inventory Write off Account'],
            ['502010002', 'Inventory Adjustment'],
        ]);

        // Indirect Expense
        $hrExpenses = $this->createAccount('05', '51', '10', '51101', 'HR Related Expenses');
        $this->createChildAccounts($hrExpenses, [
            ['511010001', 'Recruitment Charges Staff'],
            ['511010002', 'Visa & Immigration Staff Exp'],
            ['511010003', 'Air Ticket Expenses Staff'],
            ['511010004', 'Medical Expenses Staff'],
            ['511010005', 'Salaries Staff'],
            ['511010006', 'Leave Salary Staff'],
            ['511010007', 'Bonus Staff'],
            ['511010008', 'End of Service Benefit Staff'],
            ['511010009', 'Staff Medcial Insurance'],
            ['511010010', 'Staff Salaries-HO'],
            ['511010011', 'Prior Period Expense Air Ticket'],
            ['511010012', 'Employee Compensation A/c'],
        ]);

        $adminExpenses = $this->createAccount('05', '51', '20', '51201', 'Administrative Expenses');
        $this->createChildAccounts($adminExpenses, [
            ['512010001', 'Printing & Stationary'],
            ['512010002', 'Telephone & Internet'],
            ['512010003', 'Electricity & Water'],
            ['512010004', 'Legal & Professional Expenses'],
            ['512010005', 'Pantry Expense'],
            ['512010006', 'Repair & Maintenance'],
            ['512010007', 'Transportation Expense'],
            ['512010008', 'Postage & Courier Charges'],
            ['512010009', 'IT Related Expense'],
            ['512010010', 'Miscellaneous Expense'],
            ['512010011', 'Warehouse Maintenance'],
            ['512010012', 'Discount Allowed'],
            ['512010013', 'Food & Beverage Exp'],
            ['512010014', 'Parking Fee'],
            ['512010015', 'Staff Accommodation Exp'],
            ['512010016', 'Cloud Hosting'],
            ['512010017', 'Warehouse Fuel Expense'],
            ['512010018', 'Write-off'],
            ['512010019', 'Pest Control'],
            ['512010020', 'Loss on Lease hold development'],
            ['512010021', 'ERP Subscription Charge'],
            ['512010022', 'Audit fee for ISO'],
            ['512010023', 'Statutory Audit fees'],
            ['512010024', 'Financial Consultant Fees'],
        ]);

        $sellingExpenses = $this->createAccount('05', '51', '30', '51301', 'Selling & Distribution Expenses');
        $this->createChildAccounts($sellingExpenses, [
            ['513010001', 'Advertising expenses'],
            ['513010002', 'Business meeting Expense'],
            ['513010003', 'Transportation for Goods Delivery'],
            ['513010004', 'Freight Clearing Expense'],
            ['513010005', 'Registration Fee For Outlets'],
            ['513010006', 'Bad Debts'],
            ['513010007', 'Project Site Expenses'],
        ]);

        $financeExpenses = $this->createAccount('05', '51', '40', '51401', 'Finance Expenses');
        $this->createChildAccounts($financeExpenses, [
            ['514010001', 'Bank Charges'],
            ['514010002', 'Loan Processing Fees'],
            ['514010003', 'LC Bank Charge'],
            ['514010004', 'Interest on Loan'],
            ['514010005', 'LC Interest'],
            ['514010006', 'Interest on Lease liability'],
            ['514010007', 'LC Renewal Fee'],
        ]);

        $vehicleExpenses = $this->createAccount('05', '51', '50', '51501', 'Vehicle Expenses');
        $this->createChildAccounts($vehicleExpenses, [
            ['515010001', 'Vehicle Fuel & Oil'],
            ['515010002', 'Vehicle Repair & Maintenance'],
            ['515010003', 'Vehicle Insurance'],
        ]);

        $depreciationExpenses = $this->createAccount('05', '51', '60', '51601', 'Depreciation');
        $this->createChildAccounts($depreciationExpenses, [
            ['516010001', 'Depreciation Exp-Computer & Accessories'],
            ['516010002', 'Depreciation Exp-Furniture & Fixtures'],
            ['516010003', 'Depreciation Exp-Leasehold Development'],
            ['516010004', 'Depreciation Exp-Machinery & Equipment'],
            ['516010005', 'Depreciation Exp-Rou Motor Vehicle'],
            ['516010006', 'Depreciation Exp-Rou Warehouse'],
            ['516010007', 'Depreciation Exp-Motor Vehicle'],
        ]);

        $rentExpenses = $this->createAccount('05', '51', '70', '51701', 'Rent Expenses');
        $this->createChildAccounts($rentExpenses, [
            ['517010001', 'Office Rent'],
            ['517010002', 'Staff Accommodation Rent'],
            ['517010003', 'Warehouse Rent'],
            ['517010004', 'Car Rent'],
            ['517010005', 'WareHouse Generator Rent'],
        ]);

        $profitLoss = $this->createAccount('05', '51', '80', '51801', 'Profit & Loss A/c');
        $this->createChildAccounts($profitLoss, [
            ['518010001', 'Profit & Loss'],
        ]);
    }

    private function createAccount($typeCode, $groupCode, $classCode, $accountCode, $name)
    {
        return ChartOfAccount::create([
            'type_code' => $typeCode,
            'group_code' => $groupCode,
            'class_code' => $classCode,
            'account_code' => $accountCode,
            'name' => $name,
            'is_active' => true,
        ]);
    }

    private function createChildAccounts($parent, $accounts)
    {
        foreach ($accounts as [$code, $name]) {
            ChartOfAccount::create([
                'type_code' => $parent->type_code,
                'group_code' => $parent->group_code,
                'class_code' => $parent->class_code,
                'account_code' => $code,
                'name' => $name,
                'parent_id' => $parent->id,
                'is_active' => true,
            ]);
        }
    }
}
