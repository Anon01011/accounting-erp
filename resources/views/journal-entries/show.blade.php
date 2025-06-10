@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Journal Entry Details</h1>
            <div class="flex space-x-2">
                <a href="{{ route('journal-entries.index') }}" class="text-gray-600 hover:text-gray-900">
                    Back to List
                </a>
                @if($journalEntry->status === 'draft')
                    <a href="{{ route('journal-entries.edit', $journalEntry) }}" class="text-indigo-600 hover:text-indigo-900">
                        Edit
                    </a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Reference Number</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $journalEntry->reference_no }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Entry Date</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $journalEntry->entry_date->format('Y-m-d') }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Status</h3>
                    <p class="mt-1">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $journalEntry->status === 'posted' ? 'bg-green-100 text-green-800' : 
                               ($journalEntry->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($journalEntry->status) }}
                        </span>
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Created By</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $journalEntry->creator->name }}</p>
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-500">Description</h3>
                <p class="mt-1 text-sm text-gray-900">{{ $journalEntry->description }}</p>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Journal Entry Items</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Debit</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Credit</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($journalEntry->items as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $item->chartOfAccount->type_code }}.{{ $item->chartOfAccount->group_code }}.{{ $item->chartOfAccount->class_code }}.{{ $item->chartOfAccount->account_code }} - {{ $item->chartOfAccount->name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $item->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        {{ number_format($item->debit, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        {{ number_format($item->credit, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-50">
                                <td colspan="2" class="px-6 py-4 text-sm font-medium text-gray-900">Total</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                    {{ number_format($journalEntry->items->sum('debit'), 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                    {{ number_format($journalEntry->items->sum('credit'), 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end space-x-2">
                @if($journalEntry->status === 'draft')
                    <form action="{{ route('journal-entries.post', $journalEntry) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                            Post Entry
                        </button>
                    </form>
                @endif
                @if($journalEntry->status === 'posted')
                    <form action="{{ route('journal-entries.void', $journalEntry) }}" 
                          method="POST"
                          onsubmit="return confirm('Are you sure you want to void this entry?');">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                            Void Entry
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 