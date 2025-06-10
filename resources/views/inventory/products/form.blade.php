@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-xl font-semibold text-gray-800">
                    {{ isset($product) ? 'Edit Product' : 'Create Product' }}
                </h2>
            </div>

            <form action="{{ isset($product) ? route('inventory.products.update', $product) : route('inventory.products.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data"
                  class="p-6">
                @csrf
                @if(isset($product))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Basic Info Card -->
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <h3 class="text-sm font-medium text-gray-700 mb-4">Basic Information</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-600">Product Name</label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           value="{{ old('name', $product->name ?? '') }}"
                                           class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                           required>
                                    @error('name')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="code" class="block text-sm font-medium text-gray-600">SKU Code</label>
                                    <input type="text" 
                                           name="code" 
                                           id="code" 
                                           value="{{ old('code', $product->code ?? '') }}"
                                           class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                           required>
                                    @error('code')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-600">Description</label>
                                    <textarea name="description" 
                                              id="description" 
                                              rows="3"
                                              class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('description', $product->description ?? '') }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Categories & Units Card -->
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <h3 class="text-sm font-medium text-gray-700 mb-4">Categories & Units</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-gray-600">Category</label>
                                    <select name="category_id" 
                                            id="category_id"
                                            class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                            required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                    {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="unit_id" class="block text-sm font-medium text-gray-600">Unit</label>
                                    <select name="unit_id" 
                                            id="unit_id"
                                            class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                            required>
                                        <option value="">Select Unit</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}"
                                                    {{ old('unit_id', $product->unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('unit_id')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Pricing Card -->
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <h3 class="text-sm font-medium text-gray-700 mb-4">Pricing</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="purchase_price" class="block text-sm font-medium text-gray-600">Purchase Price</label>
                                    <div class="mt-1 relative rounded-lg shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 text-sm">$</span>
                                        </div>
                                        <input type="number" 
                                               name="purchase_price" 
                                               id="purchase_price" 
                                               step="0.01"
                                               value="{{ old('purchase_price', $product->purchase_price ?? '') }}"
                                               class="pl-7 block w-full rounded-lg border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                               required>
                                    </div>
                                    @error('purchase_price')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="sale_price" class="block text-sm font-medium text-gray-600">Sale Price</label>
                                    <div class="mt-1 relative rounded-lg shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 text-sm">$</span>
                                        </div>
                                        <input type="number" 
                                               name="sale_price" 
                                               id="sale_price" 
                                               step="0.01"
                                               value="{{ old('sale_price', $product->sale_price ?? '') }}"
                                               class="pl-7 block w-full rounded-lg border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                               required>
                                    </div>
                                    @error('sale_price')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Stock Levels Card -->
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <h3 class="text-sm font-medium text-gray-700 mb-4">Stock Levels</h3>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label for="min_stock" class="block text-sm font-medium text-gray-600">Min</label>
                                    <input type="number" 
                                           name="min_stock" 
                                           id="min_stock" 
                                           value="{{ old('min_stock', $product->min_stock ?? '') }}"
                                           class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                           required>
                                    @error('min_stock')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="max_stock" class="block text-sm font-medium text-gray-600">Max</label>
                                    <input type="number" 
                                           name="max_stock" 
                                           id="max_stock" 
                                           value="{{ old('max_stock', $product->max_stock ?? '') }}"
                                           class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                           required>
                                    @error('max_stock')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="current_stock" class="block text-sm font-medium text-gray-600">Current</label>
                                    <input type="number" 
                                           name="current_stock" 
                                           id="current_stock" 
                                           value="{{ old('current_stock', $product->current_stock ?? '') }}"
                                           class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                           required>
                                    @error('current_stock')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Status & Image Card -->
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-700 mb-4">Status</h3>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" 
                                               name="status" 
                                               value="1"
                                               {{ old('status', $product->status ?? true) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-600">Active</span>
                                    </label>
                                </div>

                                <div>
                                    <h3 class="text-sm font-medium text-gray-700 mb-4">Product Image</h3>
                                    <input type="file" 
                                           name="image" 
                                           id="image"
                                           class="block w-full text-sm text-gray-500
                                                  file:mr-4 file:py-2 file:px-3
                                                  file:rounded-lg file:border-0
                                                  file:text-sm file:font-medium
                                                  file:bg-indigo-50 file:text-indigo-700
                                                  hover:file:bg-indigo-100">
                                    @error('image')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('inventory.products.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ isset($product) ? 'Update Product' : 'Create Product' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 