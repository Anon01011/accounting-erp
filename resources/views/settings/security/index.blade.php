@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Security Settings</h1>
        <button type="submit" form="security-form" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Save Changes
        </button>
    </div>

    <div class="bg-white shadow-sm rounded-lg">
        <div class="p-6">
            <form id="security-form" class="space-y-6">
                <!-- Password Policy -->
                <div>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Password Policy</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Minimum Password Length</label>
                            <input type="number" min="8" max="32" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Password Expiry (days)</label>
                            <input type="number" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="col-span-2">
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label class="ml-2 block text-sm text-gray-900">Require uppercase letters</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label class="ml-2 block text-sm text-gray-900">Require lowercase letters</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label class="ml-2 block text-sm text-gray-900">Require numbers</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label class="ml-2 block text-sm text-gray-900">Require special characters</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Session Settings -->
                <div>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Session Settings</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Session Timeout (minutes)</label>
                            <input type="number" min="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Maximum Login Attempts</label>
                            <input type="number" min="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Lockout Duration (minutes)</label>
                            <input type="number" min="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Two-Factor Authentication -->
                <div>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Two-Factor Authentication</h2>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label class="ml-2 block text-sm text-gray-900">Enable Two-Factor Authentication</label>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">2FA Method</label>
                                <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option>Email</option>
                                    <option>SMS</option>
                                    <option>Authenticator App</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">2FA Expiry (minutes)</label>
                                <input type="number" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- IP Restrictions -->
                <div>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">IP Restrictions</h2>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label class="ml-2 block text-sm text-gray-900">Enable IP Restrictions</label>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Allowed IP Addresses</label>
                            <textarea rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Enter IP addresses (one per line)"></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection