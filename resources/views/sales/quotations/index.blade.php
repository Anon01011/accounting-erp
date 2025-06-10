@extends('layouts.dashboard')

@section('content')
<div class="w-full px-2 sm:px-4 lg:px-8 mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Quotations</h1>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex space-x-4">
                    <div class="relative">
                        <input type="text" placeholder="Search quotations..." class="w-64 pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                    <select class="border rounded-lg px-4 py-2 focus:outline-none focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="sent">Sent</option>
                        <option value="accepted">Accepted</option>
                        <option value="rejected">Rejected</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>
                <div class="flex space-x-4">
                    <button class="px-4 py-2 bg-[#01657F] text-white rounded-lg hover:bg-[#014d61] transition-colors duration-200">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <button class="px-4 py-2 bg-[#01657F] text-white rounded-lg hover:bg-[#014d61] transition-colors duration-200">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                    <a href="{{ route('sales.quotations.create') }}" class="px-4 py-2 bg-[#01657F] text-white rounded-lg hover:bg-[#014d61] transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>Create Quotation
                    </a>
                </div>
            </div>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quotation #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valid Until</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($quotations as $quotation)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $quotation->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $quotation->reference_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $quotation->customer->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $quotation->quotation_date->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $quotation->valid_until->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($quotation->total_amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $quotation->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $quotation->status === 'sent' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $quotation->status === 'accepted' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $quotation->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $quotation->status === 'expired' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                {{ ucfirst($quotation->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                            <a href="{{ route('sales.quotations.show', $quotation) }}" class="text-[#01657F] hover:text-[#014d61] p-2 rounded-full hover:bg-[#01657F]/10" title="View">
                                <i class="fas fa-eye text-lg"></i>
                            </a>
                            <a href="{{ route('sales.quotations.edit', $quotation) }}" class="text-[#01657F] hover:text-[#014d61] p-2 rounded-full hover:bg-[#01657F]/10" title="Edit">
                                <i class="fas fa-edit text-lg"></i>
                            </a>
                            <a href="{{ route('sales.quotations.pdf', $quotation) }}" target="_blank" class="text-[#01657F] hover:text-[#014d61] p-2 rounded-full hover:bg-[#01657F]/10" title="Download PDF">
                                <i class="fas fa-file-pdf text-lg"></i>
                            </a>
                            <button onclick="sendQuotationEmail({{ $quotation->id }})" class="text-[#01657F] hover:text-[#014d61] p-2 rounded-full hover:bg-[#01657F]/10" title="Send Email">
                                <i class="fas fa-envelope text-lg"></i>
                            </button>
                            <button onclick="confirmDeleteQuotation({{ $quotation->id }})" class="text-red-600 hover:text-red-900 p-2 rounded-full hover:bg-red-50" title="Delete">
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
                    Showing {{ $quotations->firstItem() }} to {{ $quotations->lastItem() }} of {{ $quotations->total() }} entries
                </div>
                <div class="flex space-x-2">
                    {{ $quotations->links() }}
                </div>
            </div>
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
            <form id="deleteQuotationForm" method="POST" class="inline">
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
    function confirmDeleteQuotation(id) {
        const modal = document.getElementById('deleteQuotationModal');
        const form = document.getElementById('deleteQuotationForm');
        form.action = `/sales/quotations/${id}`;
        modal.classList.remove('hidden');
    }

    function closeDeleteQuotationModal() {
        const modal = document.getElementById('deleteQuotationModal');
        modal.classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('deleteQuotationModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteQuotationModal();
        }
    });

    // Close modal when pressing Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteQuotationModal();
        }
    });

    function sendQuotationEmail(id) {
        if (confirm('Are you sure you want to send this quotation via email?')) {
            fetch(`/sales/quotations/${id}/send-email`, {
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
                    alert('Quotation sent successfully!');
                } else {
                    alert('Error sending quotation: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error sending quotation');
            });
        }
    }
</script>
@endpush
@endsection 