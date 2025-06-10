@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Create New Journal Entry</h1>
            <a href="{{ route('journal-entries.index') }}" class="text-gray-600 hover:text-gray-900">
                Back to List
            </a>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('journal-entries.store') }}" method="POST" id="journalEntryForm">
                @csrf
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="reference_no" class="block text-sm font-medium text-gray-700">Reference Number</label>
                        <input type="text" name="reference_no" id="reference_no" value="{{ old('reference_no') }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="entry_date" class="block text-sm font-medium text-gray-700">Entry Date</label>
                        <input type="date" name="entry_date" id="entry_date" value="{{ old('entry_date', date('Y-m-d')) }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="2" 
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>

                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-medium text-gray-900">Journal Entry Items</h2>
                        <button type="button" onclick="addLineItem()" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-lg text-sm">
                            Add Line Item
                        </button>
                    </div>

                    <div id="lineItems" class="space-y-4">
                        <!-- Line items will be added here dynamically -->
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div class="text-right">
                            <span class="text-sm font-medium text-gray-700">Total Debit:</span>
                            <span id="totalDebit" class="ml-2 text-lg font-bold text-gray-900">0.00</span>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-medium text-gray-700">Total Credit:</span>
                            <span id="totalCredit" class="ml-2 text-lg font-bold text-gray-900">0.00</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                        Create Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let lineItemCount = 0;
    const accounts = @json($accounts);

    function addLineItem() {
        const lineItems = document.getElementById('lineItems');
        const lineItem = document.createElement('div');
        lineItem.className = 'grid grid-cols-12 gap-4 items-end';
        lineItem.innerHTML = `
            <div class="col-span-4">
                <label class="block text-sm font-medium text-gray-700">Account</label>
                <select name="items[${lineItemCount}][chart_of_account_id]" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                    <option value="">Select Account</option>
                    ${accounts.map(account => `
                        <option value="${account.id}">${account.type_code}.${account.group_code}.${account.class_code}.${account.account_code} - ${account.name}</option>
                    `).join('')}
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700">Debit</label>
                <input type="number" name="items[${lineItemCount}][debit]" step="0.01" min="0" value="0"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       onchange="updateTotals()">
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700">Credit</label>
                <input type="number" name="items[${lineItemCount}][credit]" step="0.01" min="0" value="0"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       onchange="updateTotals()">
            </div>
            <div class="col-span-3">
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <input type="text" name="items[${lineItemCount}][description]"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="col-span-1">
                <button type="button" onclick="this.parentElement.parentElement.remove(); updateTotals()"
                        class="text-red-600 hover:text-red-900">
                    Remove
                </button>
            </div>
        `;
        lineItems.appendChild(lineItem);
        lineItemCount++;
    }

    function updateTotals() {
        let totalDebit = 0;
        let totalCredit = 0;

        document.querySelectorAll('#lineItems input[type="number"]').forEach(input => {
            const value = parseFloat(input.value) || 0;
            if (input.name.includes('[debit]')) {
                totalDebit += value;
            } else if (input.name.includes('[credit]')) {
                totalCredit += value;
            }
        });

        document.getElementById('totalDebit').textContent = totalDebit.toFixed(2);
        document.getElementById('totalCredit').textContent = totalCredit.toFixed(2);
    }

    // Add initial line items
    addLineItem();
    addLineItem();
</script>
@endpush
@endsection 