@extends('layouts.dashboard')

@push('styles')
<style>
    #success-notification {
        transition: opacity 0.3s ease-in-out;
        opacity: 1;
    }
</style>
@endpush

@section('content')
<div class="py-2">
    <div class="max-w mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
            <div id="success-notification" class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md" role="alert">
                <div class="flex items-center">
                    <div class="py-1">
                        <svg class="h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">Success!</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button onclick="closeNotification()" class="inline-flex rounded-md p-1.5 text-green-500 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <span class="sr-only">Dismiss</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <form method="POST" action="{{ $action }}" class="space-y-6" id="journal-entry-form">
                    @csrf
                    @if($method)
                        @method($method)
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="reference_no" class="block text-sm font-medium text-gray-700">Reference Number</label>
                            <input type="text" name="reference_no" id="reference_no" value="{{ old('reference_no', $journal_entry->reference_no ?? $referenceNo ?? '') }}" 
                                class="mt-1 h-12 p-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring-[#01657F] sm:text-sm bg-gray-50" readonly>
                            @error('reference_no')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                        </div>

                        <div>
                            <label for="entry_date" class="block text-sm font-medium text-gray-700">Entry Date</label>
                            <input type="date" name="entry_date" id="entry_date" value="{{ old('entry_date', ($journal_entry->entry_date ?? now())->format('Y-m-d')) }}" 
                                class="mt-1 h-12 p-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring-[#01657F] sm:text-sm" required>
                            @error('entry_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3" 
                            class="mt-1 p-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring-[#01657F] sm:text-sm" required>{{ old('description', $journal_entry->description ?? '') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">Journal Entry Lines</h3>
                            <button type="button" id="add-line" class="inline-flex items-center px-4 py-2 bg-[#01657F] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#014d61] focus:bg-[#014d61] active:bg-[#013d4d] focus:outline-none focus:ring-2 focus:ring-[#01657F] focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Add Line
                                </button>
                        </div>

                        <div id="journal-lines" class="space-y-4">
                            @if(isset($journal_entry) && $journal_entry->items->isNotEmpty())
                                @foreach($journal_entry->items as $index => $item)
                                    <div class="journal-line grid grid-cols-12 gap-4 p-4 bg-gray-50 rounded-lg">
                                        <div class="col-span-4">
                                            <label class="block text-sm font-medium text-gray-700">Account</label>
                                            <select name="items[{{ $index }}][chart_of_account_id]" class="mt-1 h-12 p-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring-[#01657F] sm:text-sm" required>
                                                <option value="">Select Account</option>
                                                @foreach($accounts as $account)
                                                    <option value="{{ $account->id }}" {{ old('items.' . $index . '.chart_of_account_id', $item->chart_of_account_id) == $account->id ? 'selected' : '' }}>
                                                        {{ $account->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-span-3">
                                            <label class="block text-sm font-medium text-gray-700">Debit</label>
                                            <input type="number" name="items[{{ $index }}][debit]" step="0.01" min="0" value="{{ old('items.' . $index . '.debit', $item->debit) }}"
                                                class="mt-1 h-12 p-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring-[#01657F] sm:text-sm">
                                        </div>
                                        <div class="col-span-3">
                                            <label class="block text-sm font-medium text-gray-700">Credit</label>
                                            <input type="number" name="items[{{ $index }}][credit]" step="0.01" min="0" value="{{ old('items.' . $index . '.credit', $item->credit) }}"
                                                class="mt-1 h-12 p-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring-[#01657F] sm:text-sm">
                                        </div>
                                        <div class="col-span-12 md:col-span-10">
                                            <label class="block text-sm font-medium text-gray-700">Description (Optional)</label>
                                            <textarea name="items[{{ $index }}][description]" rows="1" class="mt-1 p-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring-[#01657F] sm:text-sm">{{ old('items.' . $index . '.description', $item->description) }}</textarea>
                                        </div>
                                        <div class="col-span-1">
                                            <label class="block text-sm font-medium text-gray-700">&nbsp;</label>
                                            <button type="button" class="remove-line mt-1 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 h-12">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <!-- Default single line for create mode or if no items -->
                                <div class="journal-line grid grid-cols-12 gap-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium text-gray-700">Account</label>
                                        <select name="items[0][chart_of_account_id]" class="mt-1 h-12 p-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring-[#01657F] sm:text-sm" required>
                                            <option value="">Select Account</option>
                                            @foreach($accounts as $account)
                                                <option value="{{ $account->id }}" {{ old('items.0.chart_of_account_id') == $account->id ? 'selected' : '' }}>
                                                    {{ $account->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-3">
                                        <label class="block text-sm font-medium text-gray-700">Debit</label>
                                        <input type="number" name="items[0][debit]" step="0.01" min="0" value="{{ old('items.0.debit', '') }}"
                                            class="mt-1 h-12 p-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring-[#01657F] sm:text-sm">
                                    </div>
                                    <div class="col-span-3">
                                        <label class="block text-sm font-medium text-gray-700">Credit</label>
                                        <input type="number" name="items[0][credit]" step="0.01" min="0" value="{{ old('items.0.credit', '') }}"
                                            class="mt-1 h-12 p-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring-[#01657F] sm:text-sm">
                                    </div>
                                    <div class="col-span-1">
                                        <label class="block text-sm font-medium text-gray-700">&nbsp;</label>
                                        <button type="button" class="remove-line mt-1 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 h-12">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Totals Section -->
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="p-3 bg-white rounded-lg shadow-sm">
                                    <h4 class="text-sm font-medium text-gray-700">Total Debits</h4>
                                    <p id="debit-total" class="mt-1 text-2xl font-semibold text-gray-900">0.00</p>
                                </div>
                                <div class="p-3 bg-white rounded-lg shadow-sm">
                                    <h4 class="text-sm font-medium text-gray-700">Total Credits</h4>
                                    <p id="credit-total" class="mt-1 text-2xl font-semibold text-gray-900">0.00</p>
                                                    </div>
                                <div class="p-3 bg-white rounded-lg shadow-sm">
                                    <h4 class="text-sm font-medium text-gray-700">Balance</h4>
                                    <p id="balance" class="mt-1 text-2xl font-semibold text-gray-900">0.00</p>
                                                    </div>
                                                </div>
                            <div class="mt-4 p-3 bg-white rounded-lg shadow-sm">
                                <h4 class="text-sm font-medium text-gray-700">Status</h4>
                                <p id="balance-status" class="mt-1 text-lg font-semibold text-green-600">Balanced</p>
                        </div>
                    </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('journal-entries.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#01657F] focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#01657F] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#014d61] focus:bg-[#014d61] active:bg-[#013d4d] focus:outline-none focus:ring-2 focus:ring-[#01657F] focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ $submitText }}
                        </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/journal-entry.js'])
@endpush