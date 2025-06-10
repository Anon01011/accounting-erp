@extends('layouts.dashboard')

@section('content')
<div class="py-2">
    <div class="max-w mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Assets</h2>
                    <div class="flex items-center space-x-3">
                        <!-- Manage Categories Link -->
                        <a href="{{ route('assets.categories.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#01657F] focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v4a1 1 0 001 1h3m10-5v4a1 1 0 01-1 1h-3m-4 4h4m-2 0v4m0-4V7"></path>
                            </svg>
                            Manage Categories
                        </a>
                        <!-- Add New Asset Button -->
                        <a href="{{ route('assets.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-[#01657F] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#014d61] focus:bg-[#014d61] active:bg-[#013d4d] focus:outline-none focus:ring-2 focus:ring-[#01657F] focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add New Asset
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <x-notification type="success" :message="session('success')" />
                @endif

                @if(session('error'))
                    <x-notification type="error" :message="session('error')" />
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-[#01657F]">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Account Code</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Category</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Description</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($assets as $asset)
                                <tr class="bg-gray-50 parent-row hover:bg-gray-100 transition-colors duration-150" data-asset-id="{{ $asset->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <div class="flex items-center">
                                            <button type="button" 
                                                    class="toggle-children mr-2 text-[#01657F] hover:text-[#014d61] focus:outline-none"
                                                    data-asset-id="{{ $asset->id }}">
                                                <svg class="w-5 h-5 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </button>
                                            {{ $asset->full_account_code }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                        {{ $asset->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ config('accounting.groups.01.' . $asset->group_code, 'N/A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $asset->description }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="toggle-wrapper relative">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" 
                                                       class="toggle-switch sr-only peer" 
                                                       data-asset-id="{{ $asset->id }}"
                                                       {{ $asset->is_active ? 'checked' : '' }}>
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
                                            <a href="{{ route('assets.edit', $asset) }}" class="text-[#01657F] hover:text-[#014d61]" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('assets.destroy', $asset) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this asset?')" title="Delete">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @if($asset->children)
                                    @foreach($asset->children as $child)
                                        <tr class="child-row hidden hover:bg-gray-50 transition-colors duration-150" data-parent-id="{{ $asset->id }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 pl-12">
                                                {{ $child->full_account_code }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 pl-12">
                                                {{ $child->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ config('accounting.groups.01.' . $child->group_code, 'N/A') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $child->description }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <div class="toggle-wrapper relative">
                                                    <label class="inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" 
                                                               class="toggle-switch sr-only peer" 
                                                               data-asset-id="{{ $child->id }}"
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
                                                    <a href="{{ route('assets.edit', $child) }}" class="text-[#01657F] hover:text-[#014d61]" title="Edit">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('assets.destroy', $child) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this asset?')" title="Delete">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.querySelector('tbody');

    // Function to show notification
    function showNotification(message, type = 'success') {
        // Remove any existing notifications
        const existingNotifications = document.querySelectorAll('.notification-toast');
        existingNotifications.forEach(notification => notification.remove());

        // Create notification with modern UI
        const notification = document.createElement('div');
        notification.className = 'notification-toast fixed top-4 right-4 z-50';
        notification.innerHTML = `
            <div class="bg-white rounded-lg shadow-lg p-4 mb-4 ${type === 'success' ? 'border-l-4 border-green-500' : 'border-l-4 border-red-500'}">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        ${type === 'success' 
                            ? '<svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'
                            : '<svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>'
                        }
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium ${type === 'success' ? 'text-green-800' : 'text-red-800'}">
                            ${message}
                        </p>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(notification);

        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Function to handle toggle button clicks using event delegation
    function handleToggleClick(event) {
        const toggleBtn = event.target.closest('.toggle-children');
        if (!toggleBtn) return;

        const assetId = toggleBtn.dataset.assetId;
        const parentRow = toggleBtn.closest('.parent-row');
        const childRows = document.querySelectorAll(`.child-row[data-parent-id="${assetId}"]`);
        const icon = toggleBtn.querySelector('svg');
        
        // Toggle child rows visibility
        childRows.forEach(row => {
            row.classList.toggle('hidden');
        });
        
        // Rotate the arrow icon
        icon.classList.toggle('rotate-90');
        
        // Toggle parent row background
        parentRow.classList.toggle('bg-gray-50');
        parentRow.classList.toggle('bg-gray-100');
    }

    // Add event delegation for toggle buttons
    tbody.addEventListener('click', handleToggleClick);

    // Add event delegation for toggle switches
    tbody.addEventListener('change', async function(event) {
        const toggleSwitch = event.target.closest('.toggle-switch');
        if (!toggleSwitch) return;

        const assetId = toggleSwitch.dataset.assetId;
        const isActive = toggleSwitch.checked ? 1 : 0;
        const originalState = !toggleSwitch.checked;
        const toggleWrapper = toggleSwitch.closest('.toggle-wrapper');
        const loadingSpinner = toggleWrapper.querySelector('.loading-spinner');

        toggleSwitch.disabled = true;
        loadingSpinner.classList.remove('hidden');

        try {
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            formData.append('is_active', isActive);

            const response = await fetch(`/assets/${assetId}/status`, {
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

            // Update child accounts in real-time
            if (data.children_updated) {
                const childRows = document.querySelectorAll(`.child-row[data-parent-id="${assetId}"]`);
                childRows.forEach(row => {
                    const childToggle = row.querySelector('.toggle-switch');
                    if (childToggle) {
                        childToggle.checked = isActive === 1;
                    }
                });
            }

        } catch (error) {
            console.error('Error:', error);
            toggleSwitch.checked = originalState;
            showNotification(error.message || 'Failed to update status', 'error');
        } finally {
            toggleSwitch.disabled = false;
            loadingSpinner.classList.add('hidden');
        }
    });
});
</script>
@endpush
