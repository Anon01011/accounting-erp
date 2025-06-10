@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Sales Settings</h1>
        <button type="submit" form="settings-form" class="bg-theme-color text-white px-4 py-2 rounded-lg hover:bg-theme-hover">
            <i class="fas fa-save mr-2"></i>Save Changes
        </button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <form id="settings-form" class="p-6 space-y-8">
            <!-- General Settings -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">General Settings</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Default Currency</label>
                        <select name="default_currency" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-theme-color focus:border-theme-color">
                            <option value="USD">USD - US Dollar</option>
                            <option value="EUR">EUR - Euro</option>
                            <option value="GBP">GBP - British Pound</option>
                            <option value="JPY">JPY - Japanese Yen</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Default Tax Rate (%)</label>
                        <input type="number" name="default_tax_rate" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-theme-color focus:border-theme-color">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Default Payment Terms (days)</label>
                        <input type="number" name="default_payment_terms" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-theme-color focus:border-theme-color">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Default Quotation Validity (days)</label>
                        <input type="number" name="quotation_validity" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-theme-color focus:border-theme-color">
                    </div>
                </div>
            </div>

            <!-- Numbering Settings -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Numbering Settings</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Quotation Prefix</label>
                        <input type="text" name="quotation_prefix" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-theme-color focus:border-theme-color">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Order Prefix</label>
                        <input type="text" name="order_prefix" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-theme-color focus:border-theme-color">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Invoice Prefix</label>
                        <input type="text" name="invoice_prefix" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-theme-color focus:border-theme-color">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Return Prefix</label>
                        <input type="text" name="return_prefix" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-theme-color focus:border-theme-color">
                    </div>
                </div>
            </div>

            <!-- Email Settings -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Email Settings</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Default Email Template</label>
                        <select name="default_email_template" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-theme-color focus:border-theme-color">
                            <option value="default">Default Template</option>
                            <option value="modern">Modern Template</option>
                            <option value="minimal">Minimal Template</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email Signature</label>
                        <textarea name="email_signature" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-theme-color focus:border-theme-color"></textarea>
                    </div>
                </div>
            </div>

            <!-- Notification Settings -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Notification Settings</h2>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="email_notifications" class="h-4 w-4 text-theme-color focus:ring-theme-color border-gray-300 rounded">
                        <label class="ml-2 block text-sm text-gray-900">Enable Email Notifications</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="sms_notifications" class="h-4 w-4 text-theme-color focus:ring-theme-color border-gray-300 rounded">
                        <label class="ml-2 block text-sm text-gray-900">Enable SMS Notifications</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="browser_notifications" class="h-4 w-4 text-theme-color focus:ring-theme-color border-gray-300 rounded">
                        <label class="ml-2 block text-sm text-gray-900">Enable Browser Notifications</label>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection