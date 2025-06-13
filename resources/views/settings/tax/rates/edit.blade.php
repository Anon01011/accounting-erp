@extends('layouts.dashboard')

@section('content')
<div class="py-2">
    <div class="max-w mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Edit Tax Rate</h2>
                    <a href="{{ route('settings.tax.rates.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Back to List
                    </a>
                </div>

                @if($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('settings.tax.rates.update', $taxRate) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $taxRate->name) }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50"
                                   required>
                        </div>

                        <div>
                            <label for="tax_group_id" class="block text-sm font-medium text-gray-700">Tax Group</label>
                            <select name="tax_group_id" id="tax_group_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50"
                                    required>
                                <option value="">Select Tax Group</option>
                                @foreach($taxGroups as $group)
                                    <option value="{{ $group->id }}" {{ old('tax_group_id', $taxRate->tax_group_id) == $group->id ? 'selected' : '' }}>
                                        {{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="rate" class="block text-sm font-medium text-gray-700">Rate</label>
                            <input type="number" name="rate" id="rate" value="{{ old('rate', $taxRate->rate) }}" step="0.01" min="0" max="999.99"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50"
                                   required>
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                            <select name="type" id="type" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50"
                                    required>
                                <option value="percentage" {{ old('type', $taxRate->type) == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                <option value="fixed" {{ old('type', $taxRate->type) == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                            </select>
                        </div>

                        <div>
                            <label for="effective_from" class="block text-sm font-medium text-gray-700">Effective From</label>
                            <input type="date" name="effective_from" id="effective_from" value="{{ old('effective_from', $taxRate->effective_from->format('Y-m-d')) }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50"
                                   required>
                        </div>

                        <div>
                            <label for="effective_to" class="block text-sm font-medium text-gray-700">Effective To</label>
                            <input type="date" name="effective_to" id="effective_to" value="{{ old('effective_to', $taxRate->effective_to ? $taxRate->effective_to->format('Y-m-d') : '') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50">
                        </div>

                        <div>
                            <label for="is_active" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="is_active" id="is_active" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50">
                                <option value="1" {{ old('is_active', $taxRate->is_active) == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $taxRate->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div>
                            <label for="is_default" class="block text-sm font-medium text-gray-700">Default Rate</label>
                            <select name="is_default" id="is_default" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50">
                                <option value="0" {{ old('is_default', $taxRate->is_default) == '0' ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('is_default', $taxRate->is_default) == '1' ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3" 
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50">{{ old('description', $taxRate->description) }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-[#01657F] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#014d61] focus:bg-[#014d61] active:bg-[#013d4d] focus:outline-none focus:ring-2 focus:ring-[#01657F] focus:ring-offset-2 transition ease-in-out duration-150">
                            Update Tax Rate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection