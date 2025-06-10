@extends('layouts.dashboard')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('chart-of-accounts.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Type Code -->
                            <div>
                                <label for="type_code" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Type Code') }}</label>
                                <input id="type_code" name="type_code" type="text" class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200" 
                                    :value="old('type_code')" required autofocus />
                                @error('type_code')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Group Code -->
                            <div>
                                <label for="group_code" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Group Code') }}</label>
                                <input id="group_code" name="group_code" type="text" class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200" 
                                    :value="old('group_code')" required />
                                @error('group_code')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Class Code -->
                            <div>
                                <label for="class_code" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Class Code') }}</label>
                                <input id="class_code" name="class_code" type="text" class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200" 
                                    :value="old('class_code')" required />
                                @error('class_code')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Account Code -->
                            <div>
                                <label for="account_code" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Account Code') }}</label>
                                <input id="account_code" name="account_code" type="text" class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200" 
                                    :value="old('account_code')" required />
                                @error('account_code')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Name -->
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Account Name') }}</label>
                                <input id="name" name="name" type="text" class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200" 
                                    :value="old('name')" required />
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Description') }}</label>
                                <textarea id="description" name="description" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Parent Account -->
                            <div class="md:col-span-2">
                                <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Parent Account') }}</label>
                                <select id="parent_id" name="parent_id" class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200" onchange="generateCodes()">
                                    <option value="">Select Parent Account</option>
                                    @foreach($parentAccounts as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
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
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-[#01657F] shadow-sm focus:ring-[#01657F]" 
                                        {{ old('is_active', true) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-600">{{ __('Active') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <button type="button" onclick="window.history.back()" class="mr-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit" class="bg-[#01657F] hover:bg-[#014d61] text-white font-bold py-2 px-4 rounded">
                                {{ __('Create Account') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function generateCodes() {
            const parentSelect = document.getElementById('parent_id');
            const typeCodeInput = document.getElementById('type_code');
            const groupCodeInput = document.getElementById('group_code');
            const classCodeInput = document.getElementById('class_code');
            const accountCodeInput = document.getElementById('account_code');
            const selectedOption = parentSelect.options[parentSelect.selectedIndex];
            const parentCode = selectedOption.text.split(' - ')[0]; // Extract the parent code
            console.log('Parent Code:', parentCode); // Debugging: Log the parent code

            // Assuming the parent code is in the format '111020005' (Type: 11, Group: 10, Class: 20, Account: 005)
            const typeCode = parentCode.substring(0, 2); // Extract Type Code
            const groupCode = parentCode.substring(2, 4); // Extract Group Code
            const classCode = parentCode.substring(4, 6); // Extract Class Code
            const accountCode = parentCode.substring(6); // Extract Account Code

            typeCodeInput.value = typeCode; // Set Type Code
            groupCodeInput.value = groupCode; // Set Group Code
            classCodeInput.value = classCode; // Set Class Code
            accountCodeInput.value = parentCode; // Set Account Code
        }
    </script>
@endsection 