@extends('layouts.dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white shadow rounded-lg">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Edit Tax Group</h2>
            <a href="{{ route('settings.tax.groups.index') }}" class="inline-flex items-center px-4 py-2 bg-[#01657F] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#014d61] focus:bg-[#014d61] active:bg-[#013d4d] focus:outline-none focus:ring-2 focus:ring-[#01657F] focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Back to Tax Groups
            </a>
        </div>
        <div class="p-6">
            @if ($errors->any())
            <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <form action="{{ route('settings.tax.groups.update', $taxGroup) }}" method="POST" autocomplete="off" class="tax-group-form" id="taxGroupForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="_token" value="{{ csrf_token() }}" id="csrfToken">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-tag text-gray-400"></i>
                            </div>
                            <input type="text" name="name" id="name" value="{{ old('name', $taxGroup->name) }}" required 
                                class="pl-10 block w-full h-12 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#01657F] focus:border-[#01657F] transition @error('name') border-red-300 @enderror"
                                autocomplete="off">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700">Code</label>
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-hashtag text-gray-400"></i>
                            </div>
                            <input type="text" name="code" id="code" value="{{ $taxGroup->code }}" readonly 
                                class="pl-10 block w-full h-12 px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm text-gray-500 cursor-not-allowed"
                                autocomplete="off">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-gray-400 text-sm">Auto-generated</span>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <div class="relative mt-1">
                            <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                                <i class="fas fa-align-left text-gray-400"></i>
                            </div>
                            <textarea name="description" id="description" rows="3" 
                                class="pl-10 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#01657F] focus:border-[#01657F] transition @error('description') border-red-300 @enderror"
                                autocomplete="off">{{ old('description', $taxGroup->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="mt-8">
                    <div class="flex items-center space-x-48">
                        <div class="flex flex-col">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $taxGroup->is_active) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#01657F]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#01657F]"></div>
                            </label>
                            <span class="mt-2 text-sm font-medium text-gray-700">Active</span>
                        </div>
                        <div class="flex flex-col">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default', $taxGroup->is_default) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#01657F]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#01657F]"></div>
                            </label>
                            <span class="mt-2 text-sm font-medium text-gray-700">Default</span>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#01657F] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#014d61] focus:bg-[#014d61] active:bg-[#013d4d] focus:outline-none focus:ring-2 focus:ring-[#01657F] focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-save mr-2"></i> Update Tax Group
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configure toastr
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000",
        "extendedTimeOut": "1000",
        "preventDuplicates": true,
        "newestOnTop": true
    };

    // Clear form data on page unload
    window.addEventListener('beforeunload', function() {
        document.querySelector('.tax-group-form').reset();
    });

    // Prevent form data caching
    const form = document.querySelector('.tax-group-form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get the current CSRF token
        const token = document.getElementById('csrfToken').value;
        
        // Create FormData object
        const formData = new FormData(this);
        
        // Submit form via fetch
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData,
            credentials: 'same-origin'
        })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Validation failed');
                }
                return data;
            } else {
                // If the response is not JSON, redirect to the response URL
                window.location.href = response.url;
                return null;
            }
        })
        .then(data => {
            if (data && data.success) {
                toastr.success('Tax group updated successfully');
                setTimeout(() => {
                    window.location.href = data.redirect || '/settings/tax/groups';
                }, 1000);
            } else if (data) {
                toastr.error(data.message || 'An error occurred');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error(error.message || 'An error occurred while updating the tax group');
        });
    });

    const toggles = document.querySelectorAll('input[type="checkbox"]');
    
    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const field = this.id;
            const value = this.checked;
            
            if (window.location.pathname.includes('/edit/')) {
                const taxGroupId = window.location.pathname.split('/').pop();
                const token = document.getElementById('csrfToken').value;
                
                fetch(`/settings/tax/groups/${taxGroupId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        field: field,
                        value: value
                    }),
                    credentials: 'same-origin'
                })
                .then(async response => {
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const data = await response.json();
                        if (!response.ok) {
                            throw new Error(data.message || 'Failed to update status');
                        }
                        return data;
                    }
                    throw new Error('Invalid response format');
                })
                .then(data => {
                    if (data.success) {
                        toastr.success('Status updated successfully');
                    } else {
                        this.checked = !value;
                        toastr.error('Failed to update status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.checked = !value;
                    toastr.error(error.message || 'An error occurred while updating status');
                });
            }
        });
    });
});
</script>
@endpush
@endsection 