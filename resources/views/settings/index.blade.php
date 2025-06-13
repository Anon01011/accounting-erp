@extends('layouts.dashboard')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-8 flex items-center">
            <svg class="w-8 h-8 mr-3 text-[#01657F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Settings
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Company Settings Card -->
            <a href="{{ route('settings.company.index') }}" class="block bg-white rounded-xl shadow hover:shadow-lg transition p-6 border border-gray-100 hover:border-[#01657F]">
                <div class="flex items-center mb-4">
                    <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v4a1 1 0 001 1h3v6a1 1 0 001 1h6a1 1 0 001-1v-6h3a1 1 0 001-1V7a1 1 0 00-1-1h-3V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v2H4a1 1 0 00-1 1z" />
                    </svg>
                    <span class="text-lg font-semibold text-gray-900">Company</span>
                </div>
                <p class="text-gray-600">Manage company profile, logo, and business information.</p>
            </a>
            <!-- Users Settings Card -->
            <a href="{{ route('settings.users.index') }}" class="block bg-white rounded-xl shadow hover:shadow-lg transition p-6 border border-gray-100 hover:border-[#01657F]">
                <div class="flex items-center mb-4">
                    <svg class="w-8 h-8 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-4a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="text-lg font-semibold text-gray-900">Users</span>
                </div>
                <p class="text-gray-600">Manage users, roles, and permissions for your organization.</p>
            </a>
            <!-- Tax Management Card -->
            <a href="{{ route('settings.tax.groups.index') }}" class="block bg-white rounded-xl shadow hover:shadow-lg transition p-6 border border-gray-100 hover:border-[#01657F]">
                <div class="flex items-center mb-4">
                    <svg class="w-8 h-8 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m0 0l-6-6m6 6H3" />
                    </svg>
                    <span class="text-lg font-semibold text-gray-900">Tax Management</span>
                </div>
                <p class="text-gray-600">Configure tax groups, rates, and rules for assets and inventory.</p>
            </a>
            <!-- Security Settings Card -->
            <a href="{{ route('settings.security.index') }}" class="block bg-white rounded-xl shadow hover:shadow-lg transition p-6 border border-gray-100 hover:border-[#01657F]">
                <div class="flex items-center mb-4">
                    <svg class="w-8 h-8 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.104.896-2 2-2s2 .896 2 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2c0-1.104.896-2 2-2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 11V7a5 5 0 00-10 0v4" />
                    </svg>
                    <span class="text-lg font-semibold text-gray-900">Security</span>
                </div>
                <p class="text-gray-600">Set up security policies, password rules, and access controls.</p>
            </a>
            <!-- Localization Settings Card -->
            <a href="{{ route('settings.localization.index') }}" class="block bg-white rounded-xl shadow hover:shadow-lg transition p-6 border border-gray-100 hover:border-[#01657F]">
                <div class="flex items-center mb-4">
                    <svg class="w-8 h-8 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="text-lg font-semibold text-gray-900">Localization</span>
                </div>
                <p class="text-gray-600">Manage language, region, and currency settings for your business.</p>
            </a>
        </div>
    </div>
</div>
@endsection 