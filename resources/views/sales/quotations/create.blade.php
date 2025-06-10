@extends('layouts.dashboard')

@section('content')
<div class="w-full px-2 sm:px-4 lg:px-8 mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Create Quotation</h1>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <form action="{{ route('sales.quotations.store') }}" method="POST" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Selection -->
                <div>
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
                    <select name="customer_id" id="customer_id" required
                        class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Reference Number -->
                <div>
                    <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">Reference Number</label>
                    <input type="text" name="reference_number" id="reference_number" required
                        class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200"
                        placeholder="e.g., QT-2024-001">
                </div>

                <!-- Quotation Date -->
                <div>
                    <label for="quotation_date" class="block text-sm font-medium text-gray-700 mb-2">Quotation Date</label>
                    <input type="date" name="quotation_date" id="quotation_date" required
                        class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200">
                </div>

                <!-- Valid Until -->
                <div>
                    <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-2">Valid Until</label>
                    <input type="date" name="valid_until" id="valid_until" required
                        class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200">
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200"
                        placeholder="Enter any additional notes"></textarea>
                </div>
            </div>

            <!-- Items Section -->
            <div class="mt-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Items</h2>
                <div id="items-container">
                    <div class="item-row grid grid-cols-12 gap-4 mb-4">
                        <div class="col-span-4">
                            <select name="items[0][product_id]" required
                                class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200">
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <input type="number" name="items[0][quantity]" required min="1" step="1"
                                class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200"
                                placeholder="Qty">
                        </div>
                        <div class="col-span-2">
                            <input type="number" name="items[0][unit_price]" required min="0" step="0.01"
                                class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200"
                                placeholder="Price">
                        </div>
                        <div class="col-span-2">
                            <input type="number" name="items[0][discount]" min="0" step="0.01"
                                class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200"
                                placeholder="Discount">
                        </div>
                        <div class="col-span-1">
                            <button type="button" onclick="removeItem(this)"
                                class="w-full h-12 px-4 py-2 text-red-600 border border-red-600 rounded-lg hover:bg-red-50 transition-colors duration-200">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="button" onclick="addItem()"
                    class="mt-4 px-4 py-2 bg-[#01657F] text-white rounded-lg hover:bg-[#014d61] transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>Add Item
                </button>
            </div>

            <!-- Totals Section -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="subtotal" class="block text-sm font-medium text-gray-700 mb-2">Subtotal</label>
                    <input type="number" name="subtotal" id="subtotal" readonly
                        class="w-full h-12 px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30">
                </div>
                <div>
                    <label for="total_amount" class="block text-sm font-medium text-gray-700 mb-2">Total Amount</label>
                    <input type="number" name="total_amount" id="total_amount" readonly
                        class="w-full h-12 px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30">
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('sales.quotations.index') }}"
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-3 bg-[#01657F] text-white rounded-lg hover:bg-[#014d61] transition-colors duration-200">
                    Create Quotation
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let itemCount = 1;

    function addItem() {
        const container = document.getElementById('items-container');
        const newRow = document.createElement('div');
        newRow.className = 'item-row grid grid-cols-12 gap-4 mb-4';
        newRow.innerHTML = `
            <div class="col-span-4">
                <select name="items[${itemCount}][product_id]" required
                    class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200">
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <input type="number" name="items[${itemCount}][quantity]" required min="1" step="1"
                    class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200"
                    placeholder="Qty">
            </div>
            <div class="col-span-2">
                <input type="number" name="items[${itemCount}][unit_price]" required min="0" step="0.01"
                    class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200"
                    placeholder="Price">
            </div>
            <div class="col-span-2">
                <input type="number" name="items[${itemCount}][discount]" min="0" step="0.01"
                    class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200"
                    placeholder="Discount">
            </div>
            <div class="col-span-1">
                <button type="button" onclick="removeItem(this)"
                    class="w-full h-12 px-4 py-2 text-red-600 border border-red-600 rounded-lg hover:bg-red-50 transition-colors duration-200">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(newRow);
        itemCount++;
    }

    function removeItem(button) {
        const row = button.closest('.item-row');
        row.remove();
        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;
        const rows = document.querySelectorAll('.item-row');
        
        rows.forEach(row => {
            const quantity = parseFloat(row.querySelector('input[name$="[quantity]"]').value) || 0;
            const price = parseFloat(row.querySelector('input[name$="[unit_price]"]').value) || 0;
            const discount = parseFloat(row.querySelector('input[name$="[discount]"]').value) || 0;
            
            const rowTotal = (quantity * price) - discount;
            subtotal += rowTotal;
        });

        document.getElementById('subtotal').value = subtotal.toFixed(2);
        document.getElementById('total_amount').value = subtotal.toFixed(2);
    }

    // Add event listeners for calculation
    document.addEventListener('input', function(e) {
        if (e.target.matches('input[name$="[quantity]"], input[name$="[unit_price]"], input[name$="[discount]"]')) {
            calculateTotals();
        }
    });

    // Auto-fill price when product is selected
    document.addEventListener('change', function(e) {
        if (e.target.matches('select[name$="[product_id]"]')) {
            const option = e.target.options[e.target.selectedIndex];
            const price = option.dataset.price;
            const row = e.target.closest('.item-row');
            row.querySelector('input[name$="[unit_price]"]').value = price;
            calculateTotals();
        }
    });
</script>
@endpush
@endsection 