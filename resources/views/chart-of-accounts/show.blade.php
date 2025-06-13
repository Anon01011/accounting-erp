@extends('layouts.dashboard')

@section('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
    <div class="py-2">
        <div class="max-w mx-auto sm:px-6 lg:px-8">
            <!-- Header Section with Breadcrumbs -->
            <div class="mb-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('chart-of-accounts.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                </svg>
                                Chart of Accounts
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $account->name }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">
                    {{ $account->name }}
                </h2>
                <div class="flex space-x-3">
                    <a href="{{ route('chart-of-accounts.edit', ['chart_of_account' => $account->id]) }}" 
                       class="inline-flex items-center px-4 py-2 bg-[#01657F] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#01556F] focus:outline-none focus:ring-2 focus:ring-[#01657F] focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Account
                    </a>
                    <form action="{{ route('chart-of-accounts.destroy', ['chart_of_account' => $account->id]) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                onclick="return confirm('Are you sure you want to delete this account? This action cannot be undone.')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete Account
                        </button>
                    </form>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left Column - Main Content -->
                <div class="lg:col-span-8 space-y-6">
                <!-- Account Details Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Account Information
                                    </h3>
                                    <dl class="grid grid-cols-1 gap-4">
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <dt class="text-sm font-medium text-gray-500">Account Code</dt>
                                            <dd class="mt-1 text-sm font-mono text-gray-900">{{ $account->type_code }}.{{ $account->group_code }}.{{ $account->class_code }}.{{ $account->account_code }}</dd>
                                        </div>
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <dt class="text-sm font-medium text-gray-500">Account Name</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $account->name }}</dd>
                                        </div>
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $account->description ?? 'No description provided' }}</dd>
                                        </div>
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                                            <dd class="mt-1">
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $account->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $account->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                        Account Statistics
                                    </h3>
                                    <dl class="grid grid-cols-1 gap-4">
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <dt class="text-sm font-medium text-gray-500">Total Debits</dt>
                                            <dd class="mt-1 text-sm text-gray-900">${{ number_format($account->total_debits, 2) }}</dd>
                                        </div>
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <dt class="text-sm font-medium text-gray-500">Total Credits</dt>
                                            <dd class="mt-1 text-sm text-gray-900">${{ number_format($account->total_credits, 2) }}</dd>
                                        </div>
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <dt class="text-sm font-medium text-gray-500">Current Balance</dt>
                                            <dd class="mt-1 text-sm font-semibold {{ $account->current_balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                ${{ number_format($account->current_balance, 2) }}
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Activity and Monthly Trends -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Account Activity Graph -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                                </svg>
                                Account Activity
                            </h3>
                            <div class="h-80">
                                <canvas id="accountActivityChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Trends -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Monthly Trends
                            </h3>
                            <div class="h-80">
                                <canvas id="monthlyTrendsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                    <!-- Recent Journal Entries -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Recent Journal Entries
                                </h3>
                                <a href="{{ route('journal-entries.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-md transition-colors duration-150">
                                    View All Entries
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                            @if($recentJournalEntries->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-[#01657F]/20">
                                        <thead>
                                            <tr class="bg-[#01657F]">
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Date</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Reference</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Description</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Debit</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Credit</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-[#01657F]/10">
                                            @foreach($recentJournalEntries as $entry)
                                                <tr class="hover:bg-[#01657F]/5 transition-colors duration-150">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $entry->entry_date->format('Y-m-d') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        <div class="flex items-center">
                                                            <span class="font-mono bg-gray-100 px-2 py-1 rounded text-[#01657F]">{{ $entry->reference_number }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-900">
                                                        {{ $entry->description }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        ${{ number_format($entry->items->sum('debit'), 2) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        ${{ number_format($entry->items->sum('credit'), 2) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        <div class="flex items-center space-x-3">
                                                            <a href="{{ route('journal-entries.show', $entry->id) }}" 
                                                               class="text-[#01657F] hover:text-[#01556F] transition-colors duration-150"
                                                               title="View Journal Entry">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                                </svg>
                                                            </a>
                                                            <a href="{{ route('journal-entries.show', $entry->id) }}?print=true" 
                                                               class="text-green-600 hover:text-green-800 transition-colors duration-150"
                                                               title="Print Journal Entry"
                                                               target="_blank">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                                                </svg>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-12 bg-[#01657F]/5 rounded-lg">
                                    <svg class="mx-auto h-12 w-12 text-[#01657F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-[#01657F]">No journal entries</h3>
                                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new journal entry.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('journal-entries.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            New Journal Entry
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - Sidebar -->
                <div class="lg:col-span-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Quick Actions Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Quick Actions
                            </h3>
                            <div class="space-y-3">
                                <a href="{{ route('journal-entries.create') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-150">
                                    <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900">Create Journal Entry</span>
                                </a>
                                <a href="{{ route('financial-reports.balance-sheet') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-150">
                                    <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900">Generate Report</span>
                                </a>
                                <a href="{{ route('journal-entries.index') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-150">
                                    <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900">View Audit Trail</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Account Health Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-[#01657F] mb-4 flex items-center">
                                <div class="w-10 h-10 rounded-xl bg-[#01657F]/10 flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                Account Health
                            </h3>
                            <div class="space-y-6">
                                <!-- Activity Level -->
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700">Activity Level</span>
                                        @php
                                            $activityLevel = $recentJournalEntries->count() > 10 ? 'High' : ($recentJournalEntries->count() > 5 ? 'Medium' : 'Low');
                                            $activityColor = $recentJournalEntries->count() > 10 ? 'green' : ($recentJournalEntries->count() > 5 ? 'blue' : 'yellow');
                                            $activityPercentage = min(($recentJournalEntries->count() / 15) * 100, 100);
                                        @endphp
                                        <span class="text-sm font-semibold text-{{ $activityColor }}-600">{{ $activityLevel }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-{{ $activityColor }}-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $activityPercentage }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">{{ $recentJournalEntries->count() }} transactions in the last 30 days</p>
                                </div>

                                <!-- Balance Trend -->
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700">Balance Trend</span>
                                        @php
                                                $balanceTrend = $account->current_balance > ($account->total_credits > 0 ? $account->total_credits * 0.5 : 0) ? 'Positive' : 
                                                          ($account->current_balance > 0 ? 'Stable' : 'Negative');
                                                $trendColor = $account->current_balance > ($account->total_credits > 0 ? $account->total_credits * 0.5 : 0) ? 'green' : 
                                                        ($account->current_balance > 0 ? 'blue' : 'red');
                                                $trendPercentage = $account->total_credits > 0 ? 
                                                    min(abs(($account->current_balance / $account->total_credits) * 100), 100) : 
                                                    ($account->current_balance > 0 ? 100 : 0);
                                        @endphp
                                        <span class="text-sm font-semibold text-{{ $trendColor }}-600">{{ $balanceTrend }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-{{ $trendColor }}-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $trendPercentage }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Current balance: ${{ number_format($account->current_balance, 2) }}</p>
                                </div>

                                <!-- Transaction Frequency -->
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700">Transaction Frequency</span>
                                        @php
                                            $frequency = $recentJournalEntries->count() > 15 ? 'High' : 
                                                        ($recentJournalEntries->count() > 8 ? 'Regular' : 'Low');
                                            $frequencyColor = $recentJournalEntries->count() > 15 ? 'green' : 
                                                            ($recentJournalEntries->count() > 8 ? 'blue' : 'yellow');
                                            $frequencyPercentage = min(($recentJournalEntries->count() / 20) * 100, 100);
                                        @endphp
                                        <span class="text-sm font-semibold text-{{ $frequencyColor }}-600">{{ $frequency }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-{{ $frequencyColor }}-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $frequencyPercentage }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Average {{ number_format($recentJournalEntries->count() / 30, 1) }} transactions per day</p>
                                </div>

                                <!-- Account Status -->
                                <div class="pt-4 border-t border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700">Account Status</span>
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $account->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $account->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Last updated: {{ $account->updated_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Related Accounts -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-[#01657F] mb-4 flex items-center">
                                <div class="w-10 h-10 rounded-xl bg-[#01657F]/10 flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                Related Accounts
                            </h3>
                            <div class="space-y-3">
                                @if($relatedAccounts && $relatedAccounts->count() > 0)
                                    @foreach($relatedAccounts as $related)
                                        <a href="{{ route('chart-of-accounts.show', ['chart_of_account' => $related->id]) }}" 
                                           class="block p-4 bg-white rounded-xl border border-[#01657F]/10 hover:border-[#01657F]/30 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <h4 class="text-base font-semibold text-gray-900">{{ $related->name }}</h4>
                                                    <p class="text-sm text-gray-500 mt-1">{{ $related->type_code }}.{{ $related->group_code }}.{{ $related->class_code }}.{{ $related->account_code }}</p>
                                                </div>
                                                <div class="flex items-center">
                                                    <span class="text-sm text-[#01657F] font-medium">
                                                        {{ $related->items_count ?? 0 }} transactions
                                                    </span>
                                                    <svg class="w-5 h-5 text-[#01657F] ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                @else
                                    <div class="text-center py-8 bg-[#01657F]/5 rounded-xl">
                                        <svg class="mx-auto h-12 w-12 text-[#01657F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-[#01657F]">No related accounts found</h3>
                                        <p class="mt-1 text-sm text-gray-500">This account doesn't have any related accounts yet.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Account Activity Chart
    const activityCtx = document.getElementById('accountActivityChart').getContext('2d');
    new Chart(activityCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Debits',
                data: [12000, 19000, 15000, 25000, 22000, 30000],
                borderColor: 'rgb(59, 130, 246)',
                tension: 0.1
            }, {
                label: 'Credits',
                data: [10000, 15000, 12000, 20000, 18000, 25000],
                borderColor: 'rgb(16, 185, 129)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Monthly Trends Chart
    const trendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
    new Chart(trendsCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Net Balance',
                data: [2000, 4000, 3000, 5000, 4000, 5000],
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection 