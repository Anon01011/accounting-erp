@extends('layouts.dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Create Asset Category</h2>
                <form action="{{ route('assets.categories.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Category Code</label>
                            <input id="code" name="code" type="text" class="w-full h-12 px-4 py-2 border border-[#1b758c] rounded-lg focus:outline-none focus:border-[#1b758c] focus:ring focus:ring-[#1b758c] focus:ring-opacity-30 transition-colors duration-200" value="{{ old('code') }}" required autofocus />
                            @error('code')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>
                            <input id="name" name="name" type="text" class="w-full h-12 px-4 py-2 border border-[#1b758c] rounded-lg focus:outline-none focus:border-[#1b758c] focus:ring focus:ring-[#1b758c] focus:ring-opacity-30 transition-colors duration-200" value="{{ old('name') }}" required />
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="description" name="description" rows="3" class="w-full px-4 py-2 border border-[#1b758c] rounded-lg focus:outline-none focus:border-[#1b758c] focus:ring focus:ring-[#1b758c] focus:ring-opacity-30 transition-colors duration-200">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="depreciation_method" class="block text-sm font-medium text-gray-700 mb-2">Depreciation Method</label>
                            <select id="depreciation_method" name="depreciation_method" class="w-full h-12 px-4 py-2 border border-[#1b758c] rounded-lg focus:outline-none focus:border-[#1b758c] focus:ring focus:ring-[#1b758c] focus:ring-opacity-30 transition-colors duration-200" required>
                                <option value="straight_line" {{ old('depreciation_method') == 'straight_line' ? 'selected' : '' }}>Straight Line</option>
                                <option value="declining_balance" {{ old('depreciation_method') == 'declining_balance' ? 'selected' : '' }}>Declining Balance</option>
                                <option value="sum_of_years" {{ old('depreciation_method') == 'sum_of_years' ? 'selected' : '' }}>Sum of Years Digits</option>
                                <option value="units_of_production" {{ old('depreciation_method') == 'units_of_production' ? 'selected' : '' }}>Units of Production</option>
                            </select>
                            @error('depreciation_method')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="default_depreciation_rate" class="block text-sm font-medium text-gray-700 mb-2">Default Depreciation Rate (%)</label>
                            <input id="default_depreciation_rate" name="default_depreciation_rate" type="number" step="0.01" min="0" max="100" class="w-full h-12 px-4 py-2 border border-[#1b758c] rounded-lg focus:outline-none focus:border-[#1b758c] focus:ring focus:ring-[#1b758c] focus:ring-opacity-30 transition-colors duration-200" value="{{ old('default_depreciation_rate', 0) }}" required />
                            @error('default_depreciation_rate')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="default_useful_life" class="block text-sm font-medium text-gray-700 mb-2">Default Useful Life (Years)</label>
                            <input id="default_useful_life" name="default_useful_life" type="number" min="1" class="w-full h-12 px-4 py-2 border border-[#1b758c] rounded-lg focus:outline-none focus:border-[#1b758c] focus:ring focus:ring-[#1b758c] focus:ring-opacity-30 transition-colors duration-200" value="{{ old('default_useful_life', 1) }}" required />
                            @error('default_useful_life')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('assets.categories.index') }}" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1b758c]">
                            Cancel
                        </a>
                        <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white !bg-[#1b758c] hover:!bg-[#155c70] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1b758c]">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 