@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-plus-circle mr-2"></i>Create New Asset
                </h2>
                <a href="{{ route('assets.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Assets
                </a>
            </div>

            <form action="{{ route('assets.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tag mr-2"></i>Asset Name
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-layer-group mr-2"></i>Asset Category
                        </label>
                        <select name="category_id" id="category_id" required
                            class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200">
                            <option value="">Select Category</option>
                            @foreach($assetCategories as $category)
                                <option value="{{ $category->id }}" 
                                    data-depreciation-method="{{ $category->depreciation_method }}"
                                    data-depreciation-rate="{{ $category->default_depreciation_rate }}"
                                    data-useful-life="{{ $category->default_useful_life }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-align-left mr-2"></i>Description
                    </label>
                    <textarea name="description" id="description" rows="3" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Purchase Details -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="purchase_date" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt mr-2"></i>Purchase Date
                        </label>
                        <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date') }}" required
                            class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200">
                        @error('purchase_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-dollar-sign mr-2"></i>Purchase Price
                        </label>
                        <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price') }}" required step="0.01" min="0"
                            class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200">
                        @error('purchase_price')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="serial_number" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-barcode mr-2"></i>Serial Number
                        </label>
                        <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number') }}"
                            class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200">
                        @error('serial_number')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Depreciation Details -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="depreciation_method" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calculator mr-2"></i>Depreciation Method
                        </label>
                        <select name="depreciation_method" id="depreciation_method" required
                            class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200">
                            <option value="straight_line" {{ old('depreciation_method') == 'straight_line' ? 'selected' : '' }}>Straight Line</option>
                            <option value="declining_balance" {{ old('depreciation_method') == 'declining_balance' ? 'selected' : '' }}>Declining Balance</option>
                            <option value="sum_of_years" {{ old('depreciation_method') == 'sum_of_years' ? 'selected' : '' }}>Sum of Years</option>
                            <option value="double_declining" {{ old('depreciation_method') == 'double_declining' ? 'selected' : '' }}>Double Declining</option>
                            <option value="units_of_production" {{ old('depreciation_method') == 'units_of_production' ? 'selected' : '' }}>Units of Production</option>
                        </select>
                        @error('depreciation_method')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="depreciation_rate" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-percentage mr-2"></i>Depreciation Rate (%)
                        </label>
                        <input type="number" name="depreciation_rate" id="depreciation_rate" value="{{ old('depreciation_rate') }}" required step="0.01" min="0" max="100"
                            class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200">
                        @error('depreciation_rate')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="useful_life" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-clock mr-2"></i>Useful Life (Years)
                        </label>
                        <input type="number" name="useful_life" id="useful_life" value="{{ old('useful_life') }}" required min="1"
                            class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200">
                        @error('useful_life')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Additional Details -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt mr-2"></i>Location
                        </label>
                        <input type="text" name="location" id="location" value="{{ old('location') }}" required
                            class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200">
                        @error('location')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="condition" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-check-circle mr-2"></i>Condition
                        </label>
                        <select name="condition" id="condition" required
                            class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200">
                            <option value="new" {{ old('condition') == 'new' ? 'selected' : '' }}>New</option>
                            <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>Good</option>
                            <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                            <option value="poor" {{ old('condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                            <option value="critical" {{ old('condition') == 'critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                        @error('condition')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="warranty_expiry" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-shield-alt mr-2"></i>Warranty Expiry
                        </label>
                        <input type="date" name="warranty_expiry" id="warranty_expiry" value="{{ old('warranty_expiry') }}"
                            class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200">
                        @error('warranty_expiry')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-sticky-note mr-2"></i>Notes
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('assets.index') }}" class="btn-secondary">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="bg-[#01657F] hover:bg-[#014d61] text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-save mr-2"></i>Create Asset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_id');
    const depreciationMethod = document.getElementById('depreciation_method');
    const depreciationRate = document.getElementById('depreciation_rate');
    const usefulLife = document.getElementById('useful_life');

    categorySelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption) {
            depreciationMethod.value = selectedOption.dataset.depreciationMethod;
            depreciationRate.value = selectedOption.dataset.depreciationRate;
            usefulLife.value = selectedOption.dataset.usefulLife;
        }
    });
});
</script>
@endpush
@endsection 