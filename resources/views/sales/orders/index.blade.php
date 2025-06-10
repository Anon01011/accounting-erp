@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900">Sales Orders</h2>
                <a href="{{ route('sales.orders.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#01657F] hover:bg-[#014d61] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#01657F]">
                    <i class="fas fa-plus mr-2"></i>
                    New Sales Order
                </a>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0 md:space-x-4">
                <div class="flex-1 min-w-0">
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="search" class="focus:ring-[#01657F] focus:border-[#01657F] block w-full pl-10 sm:text-sm border-gray-300 rounded-md" placeholder="Search orders...">
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <select name="status" id="status" class="focus:ring-[#01657F] focus:border-[#01657F] block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <input type="date" name="date_from" id="date_from" class="focus:ring-[#01657F] focus:border-[#01657F] block w-full sm:text-sm border-gray-300 rounded-md">
                    <input type="date" name="date_to" id="date_to" class="focus:ring-[#01657F] focus:border-[#01657F] block w-full sm:text-sm border-gray-300 rounded-md">
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($orders as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->reference_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->customer->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->order_date->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->expected_delivery_date->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($order->total_amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($order->status === 'draft') bg-gray-100 text-gray-800
                                @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                                @elseif($order->status === 'processing') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'completed') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                            <a href="{{ route('sales.orders.show', $order) }}" class="text-[#01657F] hover:text-[#014d61] p-2 rounded-full hover:bg-[#01657F]/10" title="View">
                                <i class="fas fa-eye text-lg"></i>
                            </a>
                            <a href="{{ route('sales.orders.edit', $order) }}" class="text-[#01657F] hover:text-[#014d61] p-2 rounded-full hover:bg-[#01657F]/10" title="Edit">
                                <i class="fas fa-edit text-lg"></i>
                            </a>
                            <a href="{{ route('sales.orders.pdf', $order) }}" target="_blank" class="text-[#01657F] hover:text-[#014d61] p-2 rounded-full hover:bg-[#01657F]/10" title="Download PDF">
                                <i class="fas fa-file-pdf text-lg"></i>
                            </a>
                            <button onclick="sendOrderEmail({{ $order->id }})" class="text-[#01657F] hover:text-[#014d61] p-2 rounded-full hover:bg-[#01657F]/10" title="Send Email">
                                <i class="fas fa-envelope text-lg"></i>
                            </button>
                            <button onclick="confirmDeleteOrder({{ $order->id }})" class="text-red-600 hover:text-red-900 p-2 rounded-full hover:bg-red-50" title="Delete">
                                <i class="fas fa-trash text-lg"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} entries
                </div>
                <div class="flex space-x-2">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Order Confirmation Modal -->
<div id="deleteOrderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-xl font-semibold text-gray-900">Confirm Delete Order</h3>
            <button onclick="closeDeleteOrderModal()" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="mt-4">
            <p class="text-gray-600">Are you sure you want to delete this order? This action cannot be undone.</p>
        </div>
        <div class="mt-6 flex justify-end space-x-3">
            <button onclick="closeDeleteOrderModal()" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#01657F]">
                Cancel
            </button>
            <form id="deleteOrderForm" method="POST" class="inline">
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
    function confirmDeleteOrder(id) {
        const modal = document.getElementById('deleteOrderModal');
        const form = document.getElementById('deleteOrderForm');
        form.action = `/sales/orders/${id}`;
        modal.classList.remove('hidden');
    }

    function closeDeleteOrderModal() {
        const modal = document.getElementById('deleteOrderModal');
        modal.classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('deleteOrderModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteOrderModal();
        }
    });

    // Close modal when pressing Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteOrderModal();
        }
    });

    function sendOrderEmail(id) {
        if (confirm('Are you sure you want to send this order via email?')) {
            fetch(`/sales/orders/${id}/send-email`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Order sent successfully!');
                } else {
                    alert('Error sending order: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error sending order');
            });
        }
    }
</script>
@endpush
@endsection