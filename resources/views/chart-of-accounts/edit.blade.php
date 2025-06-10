@extends('layouts.dashboard')

@section('content')
<div class="py-2">
    <div class="max-w mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Edit Account</h2>
                </div>

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <div class="bg-white rounded-2xl shadow-lg p-10 max-w mx-auto">
                    <form action="{{ route('chart-of-accounts.update', $account) }}" method="POST" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Account Type -->
                            <div>
                                <label for="type_code" class="block text-base font-semibold text-gray-700 mb-2">Account Type</label>
                                <select name="type_code" id="type_code" class="form-select block w-full h-14 px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 bg-gray-50 text-lg transition-all duration-200 shadow-sm" required>
                                    <option value="">Select Type</option>
                                    @foreach($accountTypes as $code => $name)
                                        <option value="{{ $code }}" {{ $account->type_code == $code ? 'selected' : '' }}>
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
                                        <option value="{{ $code }}" {{ $account->group_code == $code ? 'selected' : '' }}>
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
                                        <option value="{{ $code }}" {{ $account->class_code == $code ? 'selected' : '' }}>
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
                                       value="{{ old('account_code', $account->account_code) }}"
                                       class="block w-full h-14 px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 bg-gray-50 text-lg transition-all duration-200 shadow-sm" 
                                       required>
                                @error('account_code')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Account Name -->
                            <div>
                                <label for="name" class="block text-base font-semibold text-gray-700 mb-2">Account Name</label>
                                <input type="text" name="name" id="name" 
                                       value="{{ old('name', $account->name) }}"
                                       class="block w-full h-14 px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 bg-gray-50 text-lg transition-all duration-200 shadow-sm" 
                                       required>
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-base font-semibold text-gray-700 mb-2">Description</label>
                                <textarea name="description" id="description" rows="3" 
                                          class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 bg-gray-50 text-lg transition-all duration-200 shadow-sm">{{ old('description', $account->description) }}</textarea>
                                @error('description')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Parent Account -->
                            <div class="md:col-span-2">
                                <label for="parent_id" class="block text-base font-semibold text-gray-700 mb-2">Parent Account</label>
                                <select name="parent_id" id="parent_id" class="form-select block w-full h-14 px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 bg-gray-50 text-lg transition-all duration-200 shadow-sm">
                                    <option value="">None</option>
                                    @foreach($parentAccounts as $parent)
                                        <option value="{{ $parent->id }}" {{ $account->parent_id == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->account_code }} - {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="md:col-span-2">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" 
                                           class="sr-only peer" 
                                           {{ old('is_active', $account->is_active) ? 'checked' : '' }}>
                                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#01657F]/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#01657F]"></div>
                                    <span class="ms-3 text-sm font-medium text-gray-700">Active</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('chart-of-accounts.index') }}" 
                               class="mr-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" class="bg-[#01657F] hover:bg-[#014d61] text-white font-bold py-2 px-4 rounded">
                                Update Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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