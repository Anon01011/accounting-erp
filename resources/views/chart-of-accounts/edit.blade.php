@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">Edit Account</h1>
        <a href="{{ route('chart-of-accounts.index') }}" 
           class="inline-flex items-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-5 rounded-lg shadow-sm transition-all duration-200">
            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Back to List
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 shadow" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 shadow" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg p-10 max-w mx-auto">
        <form action="{{ route('chart-of-accounts.update', $chartOfAccount) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Account Type -->
                <div>
                    <label for="type_code" class="block text-base font-semibold text-gray-700 mb-2">Account Type</label>
                    <select name="type_code" id="type_code" class="form-select block w-full h-14 px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 bg-gray-50 text-lg transition-all duration-200 shadow-sm" required>
                        <option value="">Select Type</option>
                        @foreach($accountTypes as $code => $name)
                            <option value="{{ $code }}" {{ $chartOfAccount->type_code == $code ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('type_code')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Group -->
                <div>
                    <label for="group_code" class="block text-base font-semibold text-gray-700 mb-2">Account Group</label>
                    <select name="group_code" id="group_code" class="form-select block w-full h-14 px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 bg-gray-50 text-lg transition-all duration-200 shadow-sm" required>
                        <option value="">Select Group</option>
                        @foreach($accountGroups as $code => $name)
                            <option value="{{ $code }}" {{ $chartOfAccount->group_code == $code ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('group_code')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Class -->
                <div>
                    <label for="class_code" class="block text-base font-semibold text-gray-700 mb-2">Account Class</label>
                    <select name="class_code" id="class_code" class="form-select block w-full h-14 px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 bg-gray-50 text-lg transition-all duration-200 shadow-sm" required>
                        <option value="">Select Class</option>
                        @foreach($accountClasses as $code => $name)
                            <option value="{{ $code }}" {{ $chartOfAccount->class_code == $code ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_code')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Code -->
                <div>
                    <label for="account_code" class="block text-base font-semibold text-gray-700 mb-2">Account Code</label>
                    <input type="text" name="account_code" id="account_code" 
                           value="{{ old('account_code', $chartOfAccount->account_code) }}"
                           class="block w-full h-14 px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 bg-gray-50 text-lg transition-all duration-200 shadow-sm" 
                           required autocomplete="off">
                    @error('account_code')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Name -->
                <div>
                    <label for="name" class="block text-base font-semibold text-gray-700 mb-2">Account Name</label>
                    <input type="text" name="name" id="name" 
                           value="{{ old('name', $chartOfAccount->name) }}"
                           class="block w-full h-14 px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 bg-gray-50 text-lg transition-all duration-200 shadow-sm" 
                           required autocomplete="off">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Parent Account -->
                <div>
                    <label for="parent_id" class="block text-base font-semibold text-gray-700 mb-2">Parent Account</label>
                    <select name="parent_id" id="parent_id" class="form-select block w-full h-14 px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 bg-gray-50 text-lg transition-all duration-200 shadow-sm">
                        <option value="">None</option>
                        @foreach($parentAccounts as $account)
                            <option value="{{ $account->id }}" {{ $chartOfAccount->parent_id == $account->id ? 'selected' : '' }}>
                                {{ $account->account_code }} - {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-base font-semibold text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="4"
                          class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 bg-gray-50 text-lg transition-all duration-200 shadow-sm resize-none" style="min-height: 56px;">{{ old('description', $chartOfAccount->description) }}</textarea>
                @error('description')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Is Active -->
            <div class="flex items-center space-x-3">
                <input type="checkbox" name="is_active" id="is_active" value="1" 
                       {{ $chartOfAccount->is_active ? 'checked' : '' }}
                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 h-6 w-6">
                <label for="is_active" class="text-base text-gray-700 font-medium">Active</label>
            </div>

            <div class="pt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white text-lg font-bold rounded-xl shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Update Account
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type_code');
    const groupSelect = document.getElementById('group_code');
    const classSelect = document.getElementById('class_code');

    // Function to update groups based on selected type
    function updateGroups() {
        const typeCode = typeSelect.value;
        if (!typeCode) {
            groupSelect.innerHTML = '<option value="">Select Group</option>';
            classSelect.innerHTML = '<option value="">Select Class</option>';
            return;
        }

        fetch(`/api/account-groups/${typeCode}`)
            .then(response => response.json())
            .then(groups => {
                groupSelect.innerHTML = '<option value="">Select Group</option>';
                Object.entries(groups).forEach(([code, name]) => {
                    groupSelect.innerHTML += `<option value="${code}">${name}</option>`;
                });
                updateClasses();
            });
    }

    // Function to update classes based on selected group
    function updateClasses() {
        const typeCode = typeSelect.value;
        const groupCode = groupSelect.value;
        if (!typeCode || !groupCode) {
            classSelect.innerHTML = '<option value="">Select Class</option>';
            return;
        }

        fetch(`/api/account-classes/${typeCode}/${groupCode}`)
            .then(response => response.json())
            .then(classes => {
                classSelect.innerHTML = '<option value="">Select Class</option>';
                Object.entries(classes).forEach(([code, name]) => {
                    classSelect.innerHTML += `<option value="${code}">${name}</option>`;
                });
            });
    }

    // Add event listeners
    typeSelect.addEventListener('change', updateGroups);
    groupSelect.addEventListener('change', updateClasses);
});
</script>
@endpush
@endsection