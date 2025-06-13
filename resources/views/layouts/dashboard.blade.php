@php
use Illuminate\Support\Facades\Auth;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self' https: 'unsafe-inline' 'unsafe-eval';">

    <title>{{ config('app.name', 'Accounting-ERP') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap CSS -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> -->

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        :root {
            --theme-color: #01657F;
            --theme-hover: #014d61;
            --sidebar-bg: #01657F;
            --sidebar-text: #ffffff;
            --sidebar-hover: rgba(255, 255, 255, 0.1);
            --sidebar-active: rgba(255, 255, 255, 0.2);
            --content-bg: #f8fafc;
            --topbar-bg: #ffffff;
        }
        
        body {
            background-color: var(--content-bg);
            font-family: 'Figtree', sans-serif;
        }

        .topbar {
            height: 64px;
            background-color: var(--topbar-bg);
            border-bottom: 1px solid #e5e7eb;
            position: fixed;
            top: 0;
            right: 0;
            left: 280px;
            z-index: 10;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
        }

        .topbar.expanded {
            left: 80px;
        }

        .search-bar {
            position: relative;
            width: 280px;
        }

        .search-bar input {
            width: 100%;
            padding: 0.5rem 1rem 0.5rem 2.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.2s;
            background-color: #f3f4f6;
        }

        .search-bar input:focus {
            outline: none;
            border-color: var(--theme-color);
            box-shadow: 0 0 0 3px rgba(1, 101, 127, 0.1);
            background-color: white;
        }

        .search-bar input::placeholder {
            color: #9ca3af;
        }

        .search-bar i {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .topbar-icon {
            width: 40px;
            height: 40px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4b5563;
            transition: all 0.2s;
            position: relative;
        }

        .topbar-icon:hover {
            background-color: #f3f4f6;
            color: var(--theme-color);
        }

        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background-color: #ef4444;
            color: white;
            font-size: 0.75rem;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .user-menu:hover {
            background-color: #f3f4f6;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--theme-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: #1f2937;
        }

        .user-role {
            font-size: 0.75rem;
            color: #6b7280;
        }

        .sidebar {
            width: 280px;
            transition: all 0.3s ease;
            background-color: var(--sidebar-bg);
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .content-wrapper {
            margin-left: 280px;
            margin-top: 64px;
            transition: all 0.3s ease;
            min-height: calc(100vh - 64px);
            padding: 2rem;
        }

        .content-wrapper.expanded {
            margin-left: 80px;
        }

        .main-content {
            padding: 2rem;
        }

        .sidebar-header {
            padding: 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
        }

        .sidebar-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: white;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .toggle-btn {
            width: 28px;
            height: 28px;
            background-color: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            color: white;
        }

        .toggle-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .toggle-btn i {
            font-size: 0.875rem;
            transition: transform 0.3s ease;
        }

        .sidebar.collapsed .toggle-btn i {
            transform: rotate(180deg);
        }

        .sidebar-nav {
            flex: 1;
            padding: 1rem 0.75rem;
            overflow-y: auto;
        }

        .menu-item {
            position: relative;
            margin: 2px 0;
        }

        .menu-link {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: all 0.2s;
            border-radius: 0.5rem;
            font-weight: 500;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .menu-link:hover {
            background-color: var(--sidebar-hover);
            opacity: 1;
        }

        .menu-link.active {
            background-color: var(--sidebar-active);
            font-weight: 600;
            opacity: 1;
        }

        .menu-icon {
            width: 1.25rem;
            height: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            opacity: 0.9;
            margin-right: 0.75rem;
        }

        .menu-text {
            flex: 1;
            white-space: nowrap;
        }

        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background-color: var(--sidebar-hover);
            border-radius: 0.5rem;
            margin: 0.25rem 0;
        }

        .submenu.active {
            max-height: 500px;
        }

        .submenu-link {
            padding: 0.625rem 0.75rem 0.625rem 2.75rem;
            font-size: 0.85rem;
            opacity: 0.8;
        }

        .submenu-link:hover {
            opacity: 1;
        }

        .chevron {
            transition: transform 0.3s ease;
            font-size: 0.75rem;
            opacity: 0.8;
            margin-left: auto;
        }

        .chevron.active {
            transform: rotate(180deg);
            opacity: 1;
        }

        .sidebar.collapsed .sidebar-title,
        .sidebar.collapsed .menu-text,
        .sidebar.collapsed .chevron {
            display: none;
        }

        .sidebar.collapsed .menu-link {
            padding: 0.75rem;
            justify-content: center;
        }

        .sidebar.collapsed .menu-icon {
            margin-right: 0;
            font-size: 1.1rem;
        }

        .sidebar.collapsed .submenu-link {
            padding: 0.625rem;
            justify-content: center;
        }

        .sidebar.collapsed .submenu {
            position: absolute;
            left: 100%;
            top: 0;
            width: 200px;
            background-color: var(--sidebar-bg);
            border-radius: 0.5rem;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            margin: 0;
            padding: 0.5rem;
        }

        .sidebar.collapsed .submenu-link {
            padding: 0.625rem 0.75rem;
        }

        .sidebar.collapsed .submenu-link .menu-icon {
            margin-right: 0.75rem;
        }

        .sidebar.collapsed .submenu-link .menu-text {
            display: block;
        }

        .card {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
            transition: all 0.3s cubic-bezier(.25,.8,.25,1);
        }

        .card:hover {
            box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
        }

        .table th {
            background-color: var(--theme-color);
            color: white;
        }

        .btn-group .btn {
            margin: 0 2px;
        }

        .badge {
            padding: 0.5em 0.75em;
        }

        .table-responsive {
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0,0,0,.125);
        }

        .card-tools {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Topbar -->
        <div class="topbar">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search transactions, customers, products..." class="w-full">
            </div>
            <div class="topbar-right">
                <a href="{{ route('sales.orders.index') }}" class="topbar-icon" title="Sales Orders">
                    <i class="fas fa-shopping-cart"></i>
                </a>
                <a href="{{ route('sales.invoices.index') }}" class="topbar-icon" title="Invoices">
                    <i class="fas fa-file-invoice-dollar"></i>
                </a>
                <div class="topbar-icon" title="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                <div class="topbar-icon" title="Messages">
                    <i class="fas fa-envelope"></i>
                    <span class="notification-badge">5</span>
                </div>
                <div class="relative">
                    <button type="button" class="user-menu group focus:outline-none" onclick="toggleUserMenu(event)">
                        <div class="user-avatar">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="user-info">
                            <div class="user-name">{{ Auth::user()->name }}</div>
                            <div class="user-role">{{ ucfirst(Auth::user()->role) }}</div>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 group-hover:text-theme-color transition-colors"></i>
                    </button>
                    
                    <!-- User Dropdown Menu -->
                    <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden z-50">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-theme-color transition-colors">
                            <i class="fas fa-user-circle mr-2"></i>Profile
                        </a>
                        <a href="{{ route('sales.settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-theme-color transition-colors">
                            <i class="fas fa-cog mr-2"></i>Settings
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="sidebar fixed top-0 left-0 h-full z-20">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <div class="logo-icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h1 class="sidebar-title">Accounting ERP</h1>
                </div>
                <button class="toggle-btn" id="sidebarToggle">
                    <i class="fas fa-chevron-left"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <!-- Dashboard -->
                <div class="menu-item">
                    <a href="{{ route('dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home menu-icon"></i>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </div>

                <!-- Sales -->
                <div class="menu-item">
                    <button class="menu-link w-full text-left" onclick="toggleSubmenu(this)">
                        <i class="fas fa-shopping-cart menu-icon"></i>
                        <span class="menu-text">Sales</span>
                        <i class="fas fa-chevron-down chevron"></i>
                    </button>
                    <div class="submenu">
                        <a href="{{ route('sales.quotations.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-file-invoice menu-icon"></i>
                            <span class="menu-text">Quotations</span>
                        </a>
                        <a href="{{ route('sales.orders.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-file-alt menu-icon"></i>
                            <span class="menu-text">Sales Orders</span>
                        </a>
                        <a href="{{ route('sales.deliveries.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-truck menu-icon"></i>
                            <span class="menu-text">Deliveries</span>
                        </a>
                        <a href="{{ route('sales.invoices.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-file-invoice-dollar menu-icon"></i>
                            <span class="menu-text">Invoices</span>
                        </a>
                        <a href="{{ route('sales.returns.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-undo menu-icon"></i>
                            <span class="menu-text">Returns</span>
                        </a>
                        <a href="{{ route('sales.payments.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-money-bill-wave menu-icon"></i>
                            <span class="menu-text">Payments</span>
                        </a>
                        <a href="{{ route('sales.reports.sales-summary') }}" class="menu-link submenu-link">
                            <i class="fas fa-chart-bar menu-icon"></i>
                            <span class="menu-text">Reports</span>
                        </a>
                        <a href="{{ route('sales.settings.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-cog menu-icon"></i>
                            <span class="menu-text">Settings</span>
                        </a>
                    </div>
                </div>

                <!-- Purchases -->
                <div class="menu-item">
                    <button class="menu-link w-full text-left" onclick="toggleSubmenu(this)">
                        <i class="fas fa-shopping-basket menu-icon"></i>
                        <span class="menu-text">Purchases</span>
                        <i class="fas fa-chevron-down chevron"></i>
                    </button>
                    <div class="submenu">
                        <a href="{{ route('purchases.orders.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-file-alt menu-icon"></i>
                            <span class="menu-text">Purchase Orders</span>
                        </a>
                        <a href="{{ route('purchases.receipts.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-truck-loading menu-icon"></i>
                            <span class="menu-text">Receipts</span>
                        </a>
                        <a href="{{ route('purchases.bills.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-file-invoice menu-icon"></i>
                            <span class="menu-text">Bills</span>
                        </a>
                        <a href="{{ route('purchases.returns.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-undo menu-icon"></i>
                            <span class="menu-text">Returns</span>
                        </a>
                    </div>
                </div>

                <!-- Inventory -->
                <div class="menu-item">
                    <button class="menu-link w-full text-left" onclick="toggleSubmenu(this)">
                        <i class="fas fa-boxes menu-icon"></i>
                        <span class="menu-text">Inventory</span>
                        <i class="fas fa-chevron-down chevron"></i>
                    </button>
                    <div class="submenu">
                        <a href="{{ route('inventory.products.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-box menu-icon"></i>
                            <span class="menu-text">Products</span>
                        </a>
                        <a href="{{ route('inventory.warehouses.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-warehouse menu-icon"></i>
                            <span class="menu-text">Warehouses</span>
                        </a>
                        <a href="{{ route('inventory.transfers.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-exchange-alt menu-icon"></i>
                            <span class="menu-text">Stock Transfers</span>
                        </a>
                        <a href="{{ route('inventory.counts.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-clipboard-check menu-icon"></i>
                            <span class="menu-text">Stock Count</span>
                        </a>
                    </div>
                </div>

                <!-- Accounting -->
                <div class="menu-item">
                    <button class="menu-link w-full text-left" onclick="toggleSubmenu(this)">
                        <i class="fas fa-calculator menu-icon"></i>
                        <span class="menu-text">Accounting</span>
                        <i class="fas fa-chevron-down chevron"></i>
                    </button>
                    <div class="submenu">
                        <a href="{{ route('chart-of-accounts.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-sitemap menu-icon"></i>
                            <span class="menu-text">Chart of Accounts</span>
                        </a>
                        <a href="{{ route('assets.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-boxes menu-icon"></i>
                            <span class="menu-text">Assets</span>
                        </a>
                        <a href="{{ route('journal-entries.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-book menu-icon"></i>
                            <span class="menu-text">Journal Entries</span>
                        </a>
                        <a href="{{ route('accounting.payments.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-money-bill-wave menu-icon"></i>
                            <span class="menu-text">Payments</span>
                        </a>
                        <a href="{{ route('accounting.receipts.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-hand-holding-usd menu-icon"></i>
                            <span class="menu-text">Receipts</span>
                        </a>
                    </div>
                </div>

                <!-- Financial Reports -->
                <div class="menu-item">
                    <button class="menu-link w-full text-left" onclick="toggleSubmenu(this)">
                        <i class="fas fa-chart-line menu-icon"></i>
                        <span class="menu-text">Financial Reports</span>
                        <i class="fas fa-chevron-down chevron"></i>
                    </button>
                    <div class="submenu">
                        <a href="{{ route('financial-reports.balance-sheet') }}" class="menu-link submenu-link">
                            <i class="fas fa-balance-scale menu-icon"></i>
                            <span class="menu-text">Balance Sheet</span>
                        </a>
                        <a href="{{ route('financial-reports.income-statement') }}" class="menu-link submenu-link">
                            <i class="fas fa-file-invoice-dollar menu-icon"></i>
                            <span class="menu-text">Income Statement</span>
                        </a>
                        <a href="{{ route('financial-reports.trial-balance') }}" class="menu-link submenu-link">
                            <i class="fas fa-calculator menu-icon"></i>
                            <span class="menu-text">Trial Balance</span>
                        </a>
                        <a href="{{ route('reports.cash-flow.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-chart-bar menu-icon"></i>
                            <span class="menu-text">Cash Flow</span>
                        </a>
                    </div>
                </div>

                <!-- CRM -->
                <div class="menu-item">
                    <button class="menu-link w-full text-left" onclick="toggleSubmenu(this)">
                        <i class="fas fa-users menu-icon"></i>
                        <span class="menu-text">CRM</span>
                        <i class="fas fa-chevron-down chevron"></i>
                    </button>
                    <div class="submenu">
                        <a href="{{ route('sales.customers.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-user-plus menu-icon"></i>
                            <span class="menu-text">Customers</span>
                        </a>
                        <a href="{{ route('sales.products.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-box menu-icon"></i>
                            <span class="menu-text">Products</span>
                        </a>
                        <a href="{{ route('sales.categories.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-tags menu-icon"></i>
                            <span class="menu-text">Categories</span>
                        </a>
                        <a href="{{ route('sales.price-lists.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-tag menu-icon"></i>
                            <span class="menu-text">Price Lists</span>
                        </a>
                    </div>
                </div>

                <!-- HR & Payroll -->
                <div class="menu-item">
                    <button class="menu-link w-full text-left" onclick="toggleSubmenu(this)">
                        <i class="fas fa-user-tie menu-icon"></i>
                        <span class="menu-text">HR & Payroll</span>
                        <i class="fas fa-chevron-down chevron"></i>
                    </button>
                    <div class="submenu">
                        <a href="{{ route('hr.employees.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-users menu-icon"></i>
                            <span class="menu-text">Employees</span>
                        </a>
                        <a href="{{ route('hr.payroll.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-money-check-alt menu-icon"></i>
                            <span class="menu-text">Payroll</span>
                        </a>
                        <a href="{{ route('hr.attendance.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-calendar-alt menu-icon"></i>
                            <span class="menu-text">Attendance</span>
                        </a>
                        <a href="{{ route('hr.documents.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-file-alt menu-icon"></i>
                            <span class="menu-text">Documents</span>
                        </a>
                    </div>
                </div>

                <!-- Settings -->
                <div class="menu-item">
                    <button class="menu-link w-full text-left" onclick="toggleSubmenu(this)">
                        <i class="fas fa-cog menu-icon"></i>
                        <span class="menu-text">Settings</span>
                        <i class="fas fa-chevron-down chevron"></i>
                    </button>
                    <div class="submenu">
                        <a href="{{ route('settings.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-cogs menu-icon"></i>
                            <span class="menu-text">Settings Overview</span>
                        </a>
                        <a href="{{ route('settings.users.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-user-cog menu-icon"></i>
                            <span class="menu-text">User Management</span>
                        </a>
                        <a href="{{ route('settings.company.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-building menu-icon"></i>
                            <span class="menu-text">Company Profile</span>
                        </a>
                        <a href="{{ route('settings.security.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-shield-alt menu-icon"></i>
                            <span class="menu-text">Security</span>
                        </a>
                        <a href="{{ route('settings.localization.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-globe menu-icon"></i>
                            <span class="menu-text">Localization</span>
                        </a>
                        <a href="{{ route('settings.tax.rates.index') }}" class="menu-link submenu-link">
                            <i class="fas fa-percentage menu-icon"></i>
                            <span class="menu-text">Tax Management</span>
                        </a>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="content-wrapper" id="content-wrapper">
            <main class="p-2">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Sidebar Toggle
        const sidebar = document.querySelector('.sidebar');
        const contentWrapper = document.querySelector('.content-wrapper');
        const topbar = document.querySelector('.topbar');
        const toggleBtn = document.getElementById('sidebarToggle');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            contentWrapper.classList.toggle('expanded');
            topbar.classList.toggle('expanded');
        });

        // User Menu Toggle
        function toggleUserMenu(event) {
            event.stopPropagation(); // Prevent event from bubbling up
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const userMenu = document.querySelector('.user-menu');
            
            if (dropdown && userMenu && !userMenu.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Submenu Toggle
        function toggleSubmenu(button) {
            const submenu = button.nextElementSibling;
            const chevron = button.querySelector('.chevron');
            
            // Close other submenus
            const allSubmenus = document.querySelectorAll('.submenu');
            const allChevrons = document.querySelectorAll('.chevron');
            
            allSubmenus.forEach(menu => {
                if (menu !== submenu && menu.classList.contains('active')) {
                    menu.classList.remove('active');
                }
            });
            
            allChevrons.forEach(chev => {
                if (chev !== chevron && chev.classList.contains('active')) {
                    chev.classList.remove('active');
                }
            });
            
            // Toggle current submenu
            submenu.classList.toggle('active');
            chevron.classList.toggle('active');
        }

        // Handle hover for collapsed sidebar
        const menuItems = document.querySelectorAll('.menu-item');
        menuItems.forEach(item => {
            item.addEventListener('mouseenter', () => {
                if (sidebar.classList.contains('collapsed')) {
                    const submenu = item.querySelector('.submenu');
                    if (submenu) {
                        submenu.style.display = 'block';
                    }
                }
            });

            item.addEventListener('mouseleave', () => {
                if (sidebar.classList.contains('collapsed')) {
                    const submenu = item.querySelector('.submenu');
                    if (submenu) {
                        submenu.style.display = '';
                    }
                }
            });
        });
    </script>

    <!-- jQuery (required for toastr) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    @stack('scripts')
</body>
</html> 