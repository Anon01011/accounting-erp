@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Edit Sales Order</h1>
        <div class="flex space-x-3">
            <a href="{{ route('sales.orders.show', $order) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                <i class="fas fa-eye mr-2"></i>View Order
            </a>
            <a href="{{ route('sales.orders.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                <i class="fas fa-arrow-left mr-2"></i>Back to Orders
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('sales.orders.update', $order) }}" method="POST" class="space-y-6" id="orderForm">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Selection -->
                <div>
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
                    <select name="customer_id" id="customer_id" required
                        class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" 
                                {{ $order->customer_id == $customer->id ? 'selected' : '' }}
                                data-currency="{{ $customer->currency ?? 'USD' }}">
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reference Number -->
                <div>
                    <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">Order Number</label>
                    <input type="text" name="reference_number" id="reference_number" required
                        class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200"
                        value="{{ old('reference_number', $order->reference_number) }}"
                        placeholder="e.g., SO-2024-001"
                        pattern="^SO-\d{4}-\d{3}$"
                        title="Format: SO-YYYY-XXX">
                    @error('reference_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Order Date -->
                <div>
                    <label for="order_date" class="block text-sm font-medium text-gray-700 mb-2">Order Date</label>
                    <input type="date" name="order_date" id="order_date" required
                        class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200"
                        value="{{ old('order_date', $order->order_date->format('Y-m-d')) }}"
                        max="{{ date('Y-m-d') }}">
                    @error('order_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expected Delivery Date -->
                <div>
                    <label for="expected_delivery_date" class="block text-sm font-medium text-gray-700 mb-2">Expected Delivery Date</label>
                    <input type="date" name="expected_delivery_date" id="expected_delivery_date" required
                        class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200"
                        value="{{ old('expected_delivery_date', $order->expected_delivery_date->format('Y-m-d')) }}"
                        min="{{ date('Y-m-d') }}">
                    @error('expected_delivery_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200"
                        placeholder="Enter any additional notes">{{ old('notes', $order->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Items Section -->
            <div class="mt-8">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-medium text-gray-900">Order Items</h2>
                    <button type="button" id="add-item" class="bg-[#01657F] text-white px-4 py-2 rounded-lg hover:bg-[#014d61]">
                        <i class="fas fa-plus mr-2"></i>Add Item
                    </button>
                </div>
                <div id="items-container">
                    @foreach($order->items as $index => $item)
                    <div class="item-row grid grid-cols-12 gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
                        <div class="col-span-4">
                            <select name="items[{{ $index }}][product_id]" required
                                class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200 product-select"
                                data-index="{{ $index }}">
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                        {{ $item->product_id == $product->id ? 'selected' : '' }}
                                        data-price="{{ $product->price }}"
                                        data-stock="{{ $product->stock }}">
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <input type="number" name="items[{{ $index }}][quantity]" placeholder="Qty" required min="1"
                                class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200 quantity-input"
                                value="{{ old("items.{$index}.quantity", $item->quantity) }}"
                                data-index="{{ $index }}">
                        </div>
                        <div class="col-span-2">
                            <input type="number" name="items[{{ $index }}][unit_price]" placeholder="Price" required min="0" step="0.01"
                                class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200 price-input"
                                value="{{ old("items.{$index}.unit_price", $item->unit_price) }}"
                                data-index="{{ $index }}">
                        </div>
                        <div class="col-span-2">
                            <input type="number" name="items[{{ $index }}][discount]" placeholder="Discount" min="0" max="100" step="0.01"
                                class="w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-30 transition-colors duration-200 discount-input"
                                value="{{ old("items.{$index}.discount", $item->discount) }}"
                                data-index="{{ $index }}">
                        </div>
                        <div class="col-span-2">
                            <button type="button" class="remove-item bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 w-full">
                                <i class="fas fa-trash mr-2"></i>Remove
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Totals Section -->
            <div class="mt-8 border-t pt-6">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-lg font-medium text-gray-900">Subtotal</span>
                    <span class="text-lg font-medium text-gray-900" id="subtotal">{{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between items-center mb-4">
                    <span class="text-lg font-medium text-gray-900">Total Discount</span>
                    <span class="text-lg font-medium text-gray-900" id="total-discount">{{ number_format($order->total_discount, 2) }}</span>
                </div>
                <div class="flex justify-between items-center mb-4">
                    <span class="text-lg font-medium text-gray-900">Total Amount</span>
                    <span class="text-lg font-medium text-gray-900" id="total">{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('sales.orders.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#01657F] hover:bg-[#014d61] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#01657F]">
                    <i class="fas fa-save mr-2"></i>Update Order
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemsContainer = document.getElementById('items-container');
    const addItemButton = document.getElementById('add-item');
    const orderForm = document.getElementById('orderForm');
    let itemCount = {{ count($order->items) }};

    // Function to calculate item total
    function calculateItemTotal(index) {
        const quantity = parseFloat(document.querySelector(`input[name="items[${index}][quantity]"]`).value) || 0;
        const unitPrice = parseFloat(document.querySelector(`input[name="items[${index}][unit_price]"]`).value) || 0;
        const discount = parseFloat(document.querySelector(`input[name="items[${index}][discount]"]`).value) || 0;
        
        const subtotal = quantity * unitPrice;
        const discountAmount = (subtotal * discount) / 100;
        return subtotal - discountAmount;
    }

    // Function to update totals
    function updateTotals() {
        let subtotal = 0;
        let totalDiscount = 0;
        
        document.querySelectorAll('.item-row').forEach((row, index) => {
            const quantity = parseFloat(row.querySelector(`input[name="items[${index}][quantity]"]`).value) || 0;
            const unitPrice = parseFloat(row.querySelector(`input[name="items[${index}][unit_price]"]`).value) || 0;
            const discount = parseFloat(row.querySelector(`input[name="items[${index}][discount]"]`).value) || 0;
            
            const itemSubtotal = quantity * unitPrice;
            const itemDiscount = (itemSubtotal * discount) / 100;
            
            subtotal += itemSubtotal;
            totalDiscount += itemDiscount;
        });

        const total = subtotal - totalDiscount;

        document.getElementById('subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('total-discount').textContent = totalDiscount.toFixed(2);
        document.getElementById('total').textContent = total.toFixed(2);
    }

    // Add new item
    addItemButton.addEventListener('click', function() {
        const newItem = document.querySelector('.item-row').cloneNode(true);
        const inputs = newItem.querySelectorAll('input, select');
        
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace(/\[\d+\]/, `[${itemCount}]`));
                input.setAttribute('data-index', itemCount);
            }
            input.value = '';
        });

        itemsContainer.appendChild(newItem);
        itemCount++;
    });

    // Remove item
    itemsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
            if (itemsContainer.children.length > 1) {
                const item = e.target.closest('.item-row');
                item.remove();
                updateTotals();
            }
        }
    });

    // Update totals when quantity, price, or discount changes
    itemsContainer.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity-input') || 
            e.target.classList.contains('price-input') || 
            e.target.classList.contains('discount-input')) {
            updateTotals();
        }
    });

    // Auto-fill price when product is selected
    itemsContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            const stock = selectedOption.getAttribute('data-stock');
            const index = e.target.getAttribute('data-index');
            
            if (price) {
                document.querySelector(`input[name="items[${index}][unit_price]"]`).value = price;
            }
            
            // Add stock validation if needed
            const quantityInput = document.querySelector(`input[name="items[${index}][quantity]"]`);
            quantityInput.setAttribute('max', stock);
            
            updateTotals();
        }
    });

    // Form validation
    orderForm.addEventListener('submit', function(e) {
        const expectedDeliveryDate = new Date(document.getElementById('expected_delivery_date').value);
        const orderDate = new Date(document.getElementById('order_date').value);
        
        if (expectedDeliveryDate < orderDate) {
            e.preventDefault();
            alert('Expected delivery date cannot be earlier than order date');
        }
    });

    // Initialize totals
    updateTotals();
});
</script>
@endpush
@endsection 