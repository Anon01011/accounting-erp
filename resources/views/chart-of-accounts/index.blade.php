@extends('layouts.dashboard')

@section('content')
<div class="py-2">
    <div class="max-w mx-auto sm:px-6 lg:px-0">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Chart of Accounts</h2>
                    <div class="flex items-center space-x-3">
                        <!-- Import Button -->
                        <button type="button" 
                                onclick="document.getElementById('importFile').click()"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#01657F] focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            Import
                        </button>
                        <input type="file" id="importFile" class="hidden" accept=".csv" onchange="handleFileImport(this)">

                        <!-- Export Button -->
                        <a href="{{ route('chart-of-accounts.export') }}" 
                           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#01657F] focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Export
                        </a>

                        <!-- Sample Template Button -->
                        <a href="{{ route('chart-of-accounts.template') }}" 
                           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#01657F] focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Sample Template
                        </a>

                        <!-- Add New Account Button -->
                        <a href="{{ route('chart-of-accounts.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-[#01657F] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#014d61] focus:bg-[#014d61] active:bg-[#013d4d] focus:outline-none focus:ring-2 focus:ring-[#01657F] focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add New Account
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <x-notification type="success" :message="session('success')" />
                @endif

                @if(session('error'))
                    <x-notification type="error" :message="session('error')" />
                @endif

                <!-- Import Progress Modal -->
                <div id="importProgressModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Importing Accounts</h3>
                            <div class="mt-2 px-7 py-3">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div id="importProgress" class="bg-[#01657F] h-2.5 rounded-full" style="width: 0%"></div>
                                </div>
                                <p id="importStatus" class="mt-2 text-sm text-gray-600">Preparing import...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-[#01657F]">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Account Code</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($accounts as $account)
                                <tr class="bg-gray-50 parent-row hover:bg-gray-100 transition-colors duration-150" data-account-id="{{ $account->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <div class="flex items-center">
                                            <button type="button" 
                                                    class="toggle-children mr-2 text-[#01657F] hover:text-[#014d61] focus:outline-none"
                                                    data-account-id="{{ $account->id }}">
                                                <svg class="w-5 h-5 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </button>
                                            {{ $account->account_code }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                        {{ $account->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ config('accounting.account_types.' . $account->type_code) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="toggle-wrapper relative">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" 
                                                       class="toggle-switch sr-only peer" 
                                                       data-account-id="{{ $account->id }}"
                                                       {{ $account->is_active ? 'checked' : '' }}>
                                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                            </label>
                                            <div class="loading-spinner hidden absolute inset-0 flex items-center justify-center">
                                                <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-3">
                                            <a href="{{ route('chart-of-accounts.show', $account) }}" class="text-[#01657F] hover:text-[#014d61]" title="View">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('chart-of-accounts.edit', $account) }}" class="text-[#01657F] hover:text-[#014d61]" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('chart-of-accounts.destroy', $account) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this account?')" title="Delete">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @foreach($account->children as $child)
                                    <tr class="child-row hidden hover:bg-gray-50 transition-colors duration-150" data-parent-id="{{ $account->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 pl-12">
                                            {{ $child->type_code }}.{{ $child->group_code }}.{{ $child->class_code }}.{{ $child->account_code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 pl-12">
                                            {{ $child->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ config('accounting.account_types.' . $child->type_code) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="toggle-wrapper relative">
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" 
                                                           class="toggle-switch sr-only peer" 
                                                           data-account-id="{{ $child->id }}"
                                                           {{ $child->is_active ? 'checked' : '' }}>
                                                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                                </label>
                                                <div class="loading-spinner hidden absolute inset-0 flex items-center justify-center">
                                                    <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-3">
                                                <a href="{{ route('chart-of-accounts.edit', $child) }}" class="text-[#01657F] hover:text-[#014d61]" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('chart-of-accounts.destroy', $child) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this account?')" title="Delete">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($hasMore)
                    <div class="mt-4 text-center">
                        <button id="loadMoreBtn" 
                                class="inline-flex items-center px-4 py-2 bg-[#01657F] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#014d61] focus:bg-[#014d61] active:bg-[#013d4d] focus:outline-none focus:ring-2 focus:ring-[#01657F] focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                            Load More
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let offset = 15;
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const tbody = document.querySelector('tbody');

    // Function to show notifications with progress bar
    function showNotification(message, type = 'success') {
        // Define colors based on type
        const colors = {
            success: {
                border: 'border-emerald-500',
                icon: 'text-emerald-500',
                progress: 'bg-emerald-500'
            },
            error: {
                border: 'border-rose-500',
                icon: 'text-rose-500',
                progress: 'bg-rose-500'
            },
            warning: {
                border: 'border-amber-500',
                icon: 'text-amber-500',
                progress: 'bg-amber-500'
            },
            info: {
                border: 'border-blue-500',
                icon: 'text-blue-500',
                progress: 'bg-blue-500'
            }
        };

        const colorScheme = colors[type] || colors.info;

        // Create notification container
        const notificationDiv = document.createElement('div');
        notificationDiv.className = `fixed top-4 right-4 w-96 bg-white rounded-lg shadow-lg z-50 overflow-hidden transform transition-all duration-300 ease-in-out ${colorScheme.border} border-l-4`;
        
        // Create notification content
        notificationDiv.innerHTML = `
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        ${type === 'success' 
                            ? '<svg class="h-6 w-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
                            : type === 'error'
                            ? '<svg class="h-6 w-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
                            : type === 'warning'
                            ? '<svg class="h-6 w-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>'
                            : '<svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                        }
                    </div>
                    <div class="ml-3 w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900">${message}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="h-1 w-full bg-gray-200">
                <div class="h-1 ${colorScheme.progress} transition-all duration-3000 ease-linear" style="width: 100%"></div>
            </div>
        `;

        // Add to document
        document.body.appendChild(notificationDiv);

        // Add slide-in animation
        setTimeout(() => {
            notificationDiv.style.transform = 'translateX(0)';
        }, 10);

        // Start progress bar animation
        const progressBar = notificationDiv.querySelector('.h-1 > div');
        progressBar.style.width = '0%';

        // Add click handler for close button
        const closeButton = notificationDiv.querySelector('button');
        closeButton.addEventListener('click', () => {
            notificationDiv.style.transform = 'translateX(100%)';
            setTimeout(() => {
                notificationDiv.remove();
            }, 300);
        });

        // Remove notification after 3 seconds
        setTimeout(() => {
            notificationDiv.style.transform = 'translateX(100%)';
            setTimeout(() => {
                notificationDiv.remove();
            }, 300);
        }, 3000);
    }

    // Initialize toggle buttons for existing rows
    document.querySelectorAll('.toggle-children').forEach(button => {
        button.addEventListener('click', function() {
            const accountId = this.dataset.accountId;
            const parentRow = this.closest('.parent-row');
            const childRows = document.querySelectorAll(`.child-row[data-parent-id="${accountId}"]`);
            const icon = this.querySelector('svg');
            
            childRows.forEach(row => {
                row.classList.toggle('hidden');
            });
            
            icon.classList.toggle('rotate-90');
            
            parentRow.classList.toggle('bg-gray-50');
            parentRow.classList.toggle('bg-gray-100');
        });
    });

    // Initialize toggle switches for existing rows
    document.querySelectorAll('.toggle-switch').forEach(toggleSwitch => {
        toggleSwitch.addEventListener('change', async function() {
            const accountId = this.dataset.accountId;
            const isActive = this.checked ? 1 : 0;
            const originalState = !this.checked;
            const toggleWrapper = this.closest('.toggle-wrapper');
            const loadingSpinner = toggleWrapper.querySelector('.loading-spinner');

            this.disabled = true;
            loadingSpinner.classList.remove('hidden');

            try {
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                formData.append('is_active', isActive);

                const response = await fetch(`/chart-of-accounts/${accountId}/status`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to update status');
                }

                showNotification(data.message || 'Status updated successfully', 'success');

                if (isActive && data.children_updated) {
                    const childRows = document.querySelectorAll(`.child-row[data-parent-id="${accountId}"]`);
                    childRows.forEach(row => {
                        const childToggle = row.querySelector('.toggle-switch');
                        if (childToggle) {
                            childToggle.checked = true;
                        }
                    });
                } else if (!isActive && data.children_updated) {
                    const childRows = document.querySelectorAll(`.child-row[data-parent-id="${accountId}"]`);
                    childRows.forEach(row => {
                        const childToggle = row.querySelector('.toggle-switch');
                        if (childToggle) {
                            childToggle.checked = false;
                        }
                    });
                }

            } catch (error) {
                console.error('Error:', error);
                this.checked = originalState;
                showNotification(error.message || 'Failed to update status', 'error');
            } finally {
                this.disabled = false;
                loadingSpinner.classList.add('hidden');
            }
        });
    });

    // Helper function to add event listeners to a row
    function addEventListeners(row) {
        // Add toggle children event listener
        const toggleBtn = row.querySelector('.toggle-children');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                const accountId = this.dataset.accountId;
                const parentRow = this.closest('.parent-row');
                const childRows = document.querySelectorAll(`.child-row[data-parent-id="${accountId}"]`);
                const icon = this.querySelector('svg');
                
                childRows.forEach(row => {
                    row.classList.toggle('hidden');
                });
                
                icon.classList.toggle('rotate-90');
                
                parentRow.classList.toggle('bg-gray-50');
                parentRow.classList.toggle('bg-gray-100');
            });
        }

        // Add toggle switch event listener
        const toggleSwitch = row.querySelector('.toggle-switch');
        if (toggleSwitch) {
            toggleSwitch.addEventListener('change', async function() {
                const accountId = this.dataset.accountId;
                const isActive = this.checked ? 1 : 0;
                const originalState = !this.checked;
                const toggleWrapper = this.closest('.toggle-wrapper');
                const loadingSpinner = toggleWrapper.querySelector('.loading-spinner');

                this.disabled = true;
                loadingSpinner.classList.remove('hidden');

                try {
                    const formData = new FormData();
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                    formData.append('is_active', isActive);

                    const response = await fetch(`/chart-of-accounts/${accountId}/status`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Failed to update status');
                    }

                    showNotification(data.message || 'Status updated successfully', 'success');

                    if (isActive && data.children_updated) {
                        const childRows = document.querySelectorAll(`.child-row[data-parent-id="${accountId}"]`);
                        childRows.forEach(row => {
                            const childToggle = row.querySelector('.toggle-switch');
                            if (childToggle) {
                                childToggle.checked = true;
                            }
                        });
                    } else if (!isActive && data.children_updated) {
                        const childRows = document.querySelectorAll(`.child-row[data-parent-id="${accountId}"]`);
                        childRows.forEach(row => {
                            const childToggle = row.querySelector('.toggle-switch');
                            if (childToggle) {
                                childToggle.checked = false;
                            }
                        });
                    }

                } catch (error) {
                    console.error('Error:', error);
                    this.checked = originalState;
                    showNotification(error.message || 'Failed to update status', 'error');
                } finally {
                    this.disabled = false;
                    loadingSpinner.classList.add('hidden');
                }
            });
        }
    }

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', async function() {
            try {
                // Show loading state
                loadMoreBtn.disabled = true;
                loadMoreBtn.innerHTML = `
                    <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Loading...
                `;

                const response = await fetch(`/chart-of-accounts/load-more?offset=${offset}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to load more accounts');
                }

                // Append new accounts to the table
                data.accounts.forEach(account => {
                    const parentRow = createAccountRow(account);
                    tbody.appendChild(parentRow);

                    // Add child rows if any
                    account.children.forEach(child => {
                        const childRow = createChildRow(child, account.id);
                        tbody.appendChild(childRow);
                    });
                });

                // Update offset
                offset += data.accounts.length;

                // Hide button if no more accounts
                if (!data.hasMore) {
                    loadMoreBtn.remove();
                }

            } catch (error) {
                console.error('Error:', error);
                showNotification(error.message || 'Failed to load more accounts', 'error');
            } finally {
                // Reset button state
                loadMoreBtn.disabled = false;
                loadMoreBtn.innerHTML = `
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                    Load More
                `;
            }
        });
    }

    // Helper function to create account row
    function createAccountRow(account) {
        const tr = document.createElement('tr');
        tr.className = 'bg-gray-50 parent-row hover:bg-gray-100 transition-colors duration-150';
        tr.dataset.accountId = account.id;
        
        tr.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                <div class="flex items-center">
                    <button type="button" 
                            class="toggle-children mr-2 text-[#01657F] hover:text-[#014d61] focus:outline-none"
                            data-account-id="${account.id}">
                        <svg class="w-5 h-5 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    ${account.account_code}
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                ${account.name}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${account.type_name || ''}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div class="toggle-wrapper relative">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" 
                               class="toggle-switch sr-only peer" 
                               data-account-id="${account.id}"
                               ${account.is_active ? 'checked' : ''}>
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                    <div class="loading-spinner hidden absolute inset-0 flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-3">
                    <a href="{{ route('chart-of-accounts.show', $account) }}" class="text-[#01657F] hover:text-[#014d61]" title="View">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </a>
                    <a href="{{ route('chart-of-accounts.edit', $account) }}" class="text-[#01657F] hover:text-[#014d61]" title="Edit">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </a>
                    <form action="{{ route('chart-of-accounts.destroy', $account) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this account?')" title="Delete">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </td>
        `;

        // Add event listeners to the new row
        addEventListeners(tr);
        return tr;
    }

    // Helper function to create child row
    function createChildRow(child, parentId) {
        const tr = document.createElement('tr');
        tr.className = 'child-row hidden hover:bg-gray-50 transition-colors duration-150';
        tr.dataset.parentId = parentId;
        
        tr.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 pl-12">
                ${child.account_code}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 pl-12">
                ${child.name}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${child.type_name || ''}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div class="toggle-wrapper relative">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" 
                               class="toggle-switch sr-only peer" 
                               data-account-id="${child.id}"
                               ${child.is_active ? 'checked' : ''}>
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                    <div class="loading-spinner hidden absolute inset-0 flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-3">
                    <a href="{{ route('chart-of-accounts.edit', $child) }}" class="text-[#01657F] hover:text-[#014d61]" title="Edit">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </a>
                    <form action="{{ route('chart-of-accounts.destroy', $child) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this account?')" title="Delete">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </td>
        `;

        // Add event listeners to the new row
        addEventListeners(tr);
        return tr;
    }

    // Function to handle file import
    window.handleFileImport = function(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (file.type !== 'text/csv' && !file.name.endsWith('.csv')) {
                showNotification('Please select a valid CSV file', 'error');
                return;
            }

            // Show progress modal
            const modal = document.getElementById('importProgressModal');
            const progressBar = document.getElementById('importProgress');
            const statusText = document.getElementById('importStatus');
            modal.classList.remove('hidden');

            // Create FormData
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            // Send file to server
            fetch('{{ route("chart-of-accounts.import") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message || 'Import completed successfully');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showNotification(data.message || 'Import failed', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred during import', 'error');
            })
            .finally(() => {
                modal.classList.add('hidden');
                input.value = ''; // Reset file input
            });
        }
    };
});
</script>
@endpush
@endsection