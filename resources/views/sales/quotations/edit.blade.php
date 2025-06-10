@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900">Edit Quotation</h2>
                <div class="flex space-x-3">
                    <a href="{{ route('sales.quotations.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#01657F] hover:bg-[#014d61] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#01657F]">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to List
                    </a>
                    <button type="button" onclick="confirmDeleteQuotation()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="fas fa-trash mr-2"></i>
                        Delete Quotation
                    </button>
                </div>
            </div>
        </div>

        <form action="{{ route('sales.quotations.update', $quotation) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Reference Number -->
                <div>
                    <label for="reference_number" class="block text-sm font-medium text-gray-700">Reference Number</label>
                    <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number', $quotation->reference_number) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50 h-10 px-3" placeholder="e.g., QT-2024-001">
                    @error('reference_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Customer -->
                <div>
                    <label for="customer_id" class="block text-sm font-medium text-gray-700">Customer</label>
                    <select name="customer_id" id="customer_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50 h-10 px-3">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id', $quotation->customer_id) == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quotation Date -->
                <div>
                    <label for="quotation_date" class="block text-sm font-medium text-gray-700">Quotation Date</label>
                    <input type="date" name="quotation_date" id="quotation_date" value="{{ old('quotation_date', $quotation->quotation_date->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50 h-10 px-3">
                    @error('quotation_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Valid Until -->
                <div>
                    <label for="valid_until" class="block text-sm font-medium text-gray-700">Valid Until</label>
                    <input type="date" name="valid_until" id="valid_until" value="{{ old('valid_until', $quotation->valid_until->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50 h-10 px-3">
                    @error('valid_until')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Items Section -->
            <div class="mt-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Items</h3>
                    <button type="button" onclick="addItem()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#01657F] hover:bg-[#014d61] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#01657F]">
                        <i class="fas fa-plus mr-2"></i>
                        Add Item
                    </button>
                </div>

                <div id="items-container">
                    @foreach($quotation->items as $index => $item)
                    <div class="item-row bg-gray-50 p-4 rounded-lg mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Product -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Product</label>
                                <select name="items[{{ $index }}][product_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50 h-10 px-3">
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Quantity -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Quantity</label>
                                <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50 h-10 px-3">
                            </div>

                            <!-- Unit Price -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Unit Price</label>
                                <input type="number" name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price }}" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50 h-10 px-3">
                            </div>

                            <!-- Discount -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Discount (%)</label>
                                <input type="number" name="items[{{ $index }}][discount]" value="{{ $item->discount }}" step="0.01" min="0" max="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50 h-10 px-3">
                            </div>

                            <!-- Tax Rate -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tax Rate (%)</label>
                                <input type="number" name="items[{{ $index }}][tax_rate]" value="{{ $item->tax_rate }}" step="0.01" min="0" max="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50 h-10 px-3">
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2 lg:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <input type="text" name="items[{{ $index }}][description]" value="{{ $item->description }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50 h-10 px-3">
                            </div>

                            <!-- Remove Button -->
                            <div class="flex items-end">
                                <button type="button" onclick="confirmDelete(this)" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <i class="fas fa-trash mr-2"></i>
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Notes -->
            <div class="mt-8">
                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50 px-3 py-2">{{ old('notes', $quotation->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-[#01657F] hover:bg-[#014d61] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#01657F]">
                    <i class="fas fa-save mr-2"></i>
                    Update Quotation
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Item Confirmation Modal -->
<div id="deleteItemModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-xl font-semibold text-gray-900">Confirm Delete Item</h3>
            <button onclick="closeDeleteItemModal()" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="mt-4">
            <p class="text-gray-600">Are you sure you want to delete this item? This action cannot be undone.</p>
        </div>
        <div class="mt-6 flex justify-end space-x-3">
            <button onclick="closeDeleteItemModal()" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#01657F]">
                Cancel
            </button>
            <button onclick="deleteItem()" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Delete
            </button>
        </div>
    </div>
</div>

<!-- Delete Quotation Confirmation Modal -->
<div id="deleteQuotationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-xl font-semibold text-gray-900">Confirm Delete Quotation</h3>
            <button onclick="closeDeleteQuotationModal()" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="mt-4">
            <p class="text-gray-600">Are you sure you want to delete this quotation? This action cannot be undone.</p>
        </div>
        <div class="mt-6 flex justify-end space-x-3">
            <button onclick="closeDeleteQuotationModal()" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#01657F]">
                Cancel
            </button>
            <form action="{{ route('sales.quotations.destroy', $quotation) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let itemCount = {{ count($quotation->items) }};
    let itemToDelete = null;

    function confirmDelete(button) {
        itemToDelete = button.closest('.item-row');
        const modal = document.getElementById('deleteItemModal');
        modal.classList.remove('hidden');
    }

    function closeDeleteItemModal() {
        const modal = document.getElementById('deleteItemModal');
        modal.classList.add('hidden');
        itemToDelete = null;
    }

    function deleteItem() {
        if (itemToDelete) {
            itemToDelete.remove();
            closeDeleteItemModal();
        }
    }

    function confirmDeleteQuotation() {
        const modal = document.getElementById('deleteQuotationModal');
        modal.classList.remove('hidden');
    }

    function closeDeleteQuotationModal() {
        const modal = document.getElementById('deleteQuotationModal');
        modal.classList.add('hidden');
    }

    // Close modals when clicking outside
    document.getElementById('deleteItemModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteItemModal();
        }
    });

    document.getElementById('deleteQuotationModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteQuotationModal();
        }
    });

    // Close modals when pressing Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteItemModal();
            closeDeleteQuotationModal();
        }
    });

    function addItem() {
        const container = document.getElementById('items-container');
        const template = `
            <div class="item-row bg-gray-50 p-4 rounded-lg mb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Product -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Product</label>
                        <select name="items[${itemCount}][product_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50 h-10 px-3">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Quantity</label>
                        <input type="number" name="items[${itemCount}][quantity]" value="1" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50 h-10 px-3">
                    </div>

                    <!-- Unit Price -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Unit Price</label>
                        <input type="number" name="items[${itemCount}][unit_price]" value="0.00" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50 h-10 px-3">
                    </div>

                    <!-- Discount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Discount (%)</label>
                        <input type="number" name="items[${itemCount}][discount]" value="0.00" step="0.01" min="0" max="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50 h-10 px-3">
                    </div>

                    <!-- Tax Rate -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tax Rate (%)</label>
                        <input type="number" name="items[${itemCount}][tax_rate]" value="0.00" step="0.01" min="0" max="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50 h-10 px-3">
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2 lg:col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <input type="text" name="items[${itemCount}][description]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50 h-10 px-3">
                    </div>

                    <!-- Remove Button -->
                    <div class="flex items-end">
                        <button type="button" onclick="removeItem(this)" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-trash mr-2"></i>
                            Remove
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', template);
        itemCount++;
    }

    function removeItem(button) {
        button.closest('.item-row').remove();
    }
</script>
@endpush
@endsection 
