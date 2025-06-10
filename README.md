# Accounting ERP

This is a Laravel-based Enterprise Resource Planning (ERP) system focused on accounting and related business processes.

## Features

- User authentication and authorization
- Chart of Accounts management
- Journal Entries and Financial Reports
- Sales, Purchases, and Inventory management
- Human Resources management including Attendance, Payroll, and Employee records
- Multi-module architecture with clear separation of concerns
- Database migrations and seeders for easy setup
- Responsive frontend built with Blade templates and Vite asset bundling
- Comprehensive automated testing setup with PHPUnit

## Project Structure

The project follows the standard Laravel framework structure with the following key directories and files:

- **app/**: Core application code
  - **Http/Controllers/**: Controllers handling HTTP requests, organized by domain (Accounting, Auth, HR, Inventory, Purchases, Reports, Sales, Settings)
  - **Models/**: Eloquent ORM models representing database tables (User, Product, Invoice, JournalEntry, etc.)
  - **Providers/**: Service providers for bootstrapping application services

- **bootstrap/**: Application bootstrap files and cache

- **config/**: Configuration files for app settings, database, cache, mail, queue, services, session, and logging

- **database/**: Database migrations, seeders, and model factories
  - **migrations/**: Scripts to create and modify database tables
  - **seeders/**: Seed data for initial setup
  - **factories/**: Factories for generating test data

- **public/**: Publicly accessible files including the main entry point (index.php), favicon, and web server config

- **resources/**: Frontend assets and Blade view templates
  - **css/**, **js/**: Stylesheets and JavaScript files
  - **views/**: Blade templates organized by feature (accounting, auth, chart-of-accounts, financial-reports, hr, inventory, journal-entries, purchases, reports, sales, settings)
  - **layouts/**: Common layout templates
  - **components/**: Reusable UI components like sidebar

- **routes/**: Route definitions for web and console commands

- **storage/**: Logs, cache, sessions, compiled views, and other runtime files

- **tests/**: Automated tests
  - **Feature/**: Feature tests
  - **Unit/**: Unit tests
  - **TestCase.php**: Base test class

## Setup Instructions

1. Clone the repository
2. Run `composer install` to install PHP dependencies
3. Run `npm install` to install frontend dependencies
4. Copy `.env.example` to `.env` and configure your environment variables
5. Run `php artisan key:generate` to generate the application key
6. Run `php artisan migrate --seed` to set up the database with initial data
7. Run `npm run dev` to build frontend assets
8. Run `php artisan serve` to start the development server

## Testing

- PHPUnit is configured for backend testing.
- Run `php artisan test` or `vendor/bin/phpunit` to execute tests.
- Frontend testing is not included by default.

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laracasts Video Tutorials](https://laracasts.com)

## License

This project is open-sourced software licensed under the MIT license.

## Chart of Accounts Structure

The Chart of Accounts is organized into the following main categories, groups, and classes with corresponding account numbers and names:

1. Assets
   - 10. Fixed Assets
     - 11101. Property, Plant and Equipment
       - Software
       - Machinery & Equipment
       - Leasehold Development
       - Computer and Accessories
       - Furniture and fixtures
       - Motor Vehicle
     - 11102. Accumulated Depreciation
       - Accumulated Dep-Software
       - Accumulated Dep-Machinery & Equipment
       - Accumulated Dep-Furniture and fixtures
       - Accumulated Dep-Leasehold Development
       - Accumulated Dep-Computer & Accessories
       - Accumulated Dep-Motor Vehicle
     - 11103. Right of use assets
       - ROU Motor Vehicle
       - ROU Warehouse
     - 11104. Accumulated Dep ROU
       - Accumulated Dep-Rou Motor vehicles
       - Accumulated Dep ROU Warehouse
   - 11. Current Assets
     - 11. Receivables
       - 10101. Prepayments A/c
         - Prepayments
         - Prepaid Medical Insurance
         - Retension Charge
         - Prepaid Registration Fees
         - Prepaid Lawyer Fees
         - Prepaid Marketing Expenses
         - Prepaid Interest Expenses
         - Prepaid Interest Exp Al Jazeera
         - Prepaid Exp for Economic Zone
         - Prepaid LC Renewal Fee
       - 10102. Staff Advances
         - Janna Advance A/c
         - Sashidharan Salary Advance
         - Angy Salary Advance
       - 10103. Accounts Receivable
         - ADVANCED ENGINEERING MAINTENANCE COMPANY(BAM)
         - ADVANCED FUTURE TECHNOLOGY
         - AFCO W.L.L.
       - 10104. Other Current Asset
         - PDC RECEIVED
         - Areeba
         - Security Deposit Fuel Tanks
         - Eren Rent Security Deposit
         - FMCG Suspense
         - Doha Modern (Old Warehouse)
         - Awqaf Guarantee
         - DFRP Lite N Appetite
         - DFRP Platinum
         - Eco Flooring Customer
         - Advance to Supplier
         - Abdul Aziz Villa
         - Charcoalite Trading
       - 10105. Legal Court Case
         - Legal Case -Marina Trading & Contracting Co.W.L.L
         - Legal Case-FastWay Trading & Contracting
         - Legal Case-Polygon Trading & Contracting
         - Legal Case-Alliance Partner Trading and Contracting
         - LEGAL CASE AETERNUM CONTRACTING
         - Legal Case - Inshah Contracting
       - 10106. FMCG Customers
         - KROM Group
         - PANDA HYPERMARKET
         - Samples
         - Salwa Family Shopping Complex
   - 12. Cash Accounts
     - 12101. Cash and Cash Equivalents
       - Cash In hand
       - Petty Cash-Mep
       - Petty Cash-Fmcg
       - Petty Cash-Warehouse
   - 13. Bank Accounts
     - 13101. Bank Accounts
       - QIIB-1111-071786-001
       - QIIB-1111-071786-002
   - 14. Stock In Hand
     - 14101. Inventory
       - Inventory
       - Pending Delivery Note
       - FMCG Inventory Account

2. Liabilities
   - 20. Current Liabilities
     - 20. Payables
       - 20101. Accounts Payable
         - FERPLAST Srl
         - INKA DIS TICARET A.S.
       - 20102. PDC Issued
         - PDC Issued
       - 20103. Other Payable
         - Due to Hassan GM-Personal Drawings
         - Freight Expense Payable A/C
         - Due to - Lite N Appetite
         - LC Charge Payable A/c
         - Hassan Credit Card
   - 21. Provisions
     - 21101. Provisions
       - Accrued Expenses
       - Accrued Payroll
       - Provision for Leave Salary
       - Almaha Insurance
       - Provisions for EOS
       - Provisions for Audit Fees
       - Provision for Inventory write off
       - Provision for Bad Debts
       - Provision for Reserve Exchange gain or loss
   - 21. Non Current Liabilities
     - 22. Long Term Loan
       - 22101. Long Term Loan
         - Lease Liability Vehicle
         - Lease Liability Warehouse

3. Capital
   - 30. Equity
     - 30101. Equity Share Capital
     - Legal Reserve
     - Retained Earnings

4. Income
   - 40. Direct Income
     - 40101. Sales Income
       - Sales
       - Sales Return
   - 41. Indirect Income
     - 61. Indirect Income
       - 40202. Other Income
         - Discount Received
         - Exchange Rate Gain or Loss
         - Other Income
         - Exchange Rate Gain or loss-HO

5. Expense
   - 50. Direct Expense
     - 50101. Cost of Sales
       - Material Cost
       - Fixed Rebate
     - 50201. Inventory Adjustment Account
       - Inventory Write off Account
       - Inventory Adjustment
   - 51. Indirect Expense
     - 51101. HR Related Expenses
       - Recruitment Charges Staff
       - Visa & Immigration Staff Exp
       - Air Ticket Expenses Staff
       - Medical Expenses Staff
       - Salaries Staff
       - Leave Salary Staff
       - Bonus Staff
       - End of Service Benefit Staff
       - Staff Medical Insurance
       - Staff Salaries-HO
       - Prior Period Expense Air Ticket
       - Employee Compensation A/c
     - 51201. Administrative Expenses
       - Printing & Stationary
       - Telephone & Internet
       - Electricity & Water
       - Legal & Professional Expenses
       - Pantry Expense
       - Repair & Maintenance
       - Transportation Expense
       - Postage & Courier Charges
       - IT Related Expense
       - Miscellaneous Expense
       - Warehouse Maintenance
       - Discount Allowed
       - Food & Beverage Exp
       - Parking Fee
       - Staff Accommodation Exp
       - Cloud Hosting
       - Warehouse Fuel Expense
       - Write-off
       - Pest Control
       - Loss on Lease hold development
       - ERP Subscription Charge
       - Audit fee for ISO
       - Statutory Audit fees
       - Financial Consultant Fees
     - 51301. Selling & Distribution Expenses
       - Advertising expenses
       - Business meeting Expense
       - Transportation for Goods Delivery
       - Freight Clearing Expense
       - Registration Fee For Outlets
       - Bad Debts
       - Project Site Expenses
     - 51401. Finance Expenses
       - Bank Charges
       - Loan Processing Fees
       - LC Bank Charge
       - Interest on Loan
       - LC Interest
       - Interest on Lease liability
       - LC Renewal Fee
     - 51501. Vehicle Expenses
       - Vehicle Fuel & Oil
       - Vehicle Repair & Maintenance
       - Vehicle Insurance
     - 51601. Depreciation
       - Depreciation Exp-Computer & Accessories
       - Depreciation Exp-Furniture & Fixtures
       - Depreciation Exp-Leasehold Development
       - Depreciation Exp-Machinery & Equipment
       - Depreciation Exp-Rou Motor Vehicle
       - Depreciation Exp-Rou Warehouse
       - Depreciation Exp-Motor Vehicle
     - 51701. Rent Expenses
       - Office Rent
       - Staff Accommodation Rent
       - Warehouse Rent.
       - Car Rent
       - Warehouse Generator Rent
     - 51801. Profit & Loss A/c
       - Profit & Loss
       - Various detailed expense and income accounts as per accounting standards

This detailed Chart of Accounts structure supports the comprehensive accounting and financial management features of the ERP system.

## Project Rules and Guidelines

To maintain the quality, consistency, and functionality of this project, please adhere to the following rules and guidelines:

1. **Coding Standards**
   - Follow PSR-12 coding standards for PHP code.
   - Use meaningful and consistent naming conventions for variables, functions, classes, and files.
   - Keep code modular and reusable; avoid duplication.

2. **Project Structure**
   - Follow the Laravel framework conventions for organizing files and directories.
   - Controllers should be placed in appropriate domain-specific subdirectories under `app/Http/Controllers/`.
   - Models should be placed in `app/Models/` and represent database tables clearly.
   - Views should be organized by feature under `resources/views/`.

3. **Database Management**
   - Use Laravel migrations for all database schema changes.
   - Seeders should be used to populate initial or test data.
   - Avoid direct database modifications outside migrations and seeders.

4. **Version Control**
   - Commit changes with clear, descriptive messages.
   - Avoid committing sensitive information or environment-specific files.
   - Use feature branches for new development and pull requests for code reviews.

5. **Testing**
   - Write automated tests for new features and bug fixes.
   - Use PHPUnit for backend testing.
   - Ensure tests cover critical paths and edge cases.
   - Run tests before committing changes.

6. **Frontend Development**
   - Use Blade templates for views.
   - Manage frontend assets with Vite.
   - Keep frontend code organized under `resources/css/` and `resources/js/`.

7. **Security**
   - Sanitize and validate all user inputs.
   - Use Laravel’s built-in authentication and authorization features.
   - Keep dependencies up to date to avoid vulnerabilities.

8. **Documentation**
   - Update README.md and other documentation with any significant changes.
   - Document new features, APIs, and configuration changes clearly.

Following these rules will help ensure the project remains maintainable, scalable, and secure.
