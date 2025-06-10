@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-medium text-gray-900">Product Details</h2>
                    <div class="flex space-x-3">
                        <a href="{{ route('inventory.products.edit', $product) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Edit Product
                        </a>
                        <form action="{{ route('inventory.products.destroy', $product) }}" 
                              method="POST" 
                              class="inline"
                              onsubmit="return confirm('Are you sure you want to delete this product?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Delete Product
                            </button>
                        </form>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Product Image -->
                    <div class="col-span-1">
                        @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-64 object-cover rounded-lg">
                        @else
                            <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                                <span class="text-gray-500">No image available</span>
                            </div>
                        @endif
                    </div>

                    <!-- Product Details -->
                    <div class="col-span-1 space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Product Name</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->name }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">SKU Code</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->code }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Category</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->category->name }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Unit</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->unit->name }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Description</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->description ?: 'No description available' }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Pricing Information -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-4">Pricing Information</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Purchase Price</span>
                                <span class="text-sm font-medium text-gray-900">${{ number_format($product->purchase_price, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Sale Price</span>
                                <span class="text-sm font-medium text-gray-900">${{ number_format($product->sale_price, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Profit Margin</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ number_format((($product->sale_price - $product->purchase_price) / $product->purchase_price) * 100, 2) }}%
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Information -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-4">Stock Information</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Current Stock</span>
                                <span class="text-sm font-medium text-gray-900">{{ $product->current_stock }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Minimum Stock</span>
                                <span class="text-sm font-medium text-gray-900">{{ $product->min_stock }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Maximum Stock</span>
                                <span class="text-sm font-medium text-gray-900">{{ $product->max_stock }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Stock Status</span>
                                <span class="text-sm font-medium {{ $product->current_stock <= $product->min_stock ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $product->current_stock <= $product->min_stock ? 'Low Stock' : 'In Stock' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('inventory.products.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Back to Products
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 