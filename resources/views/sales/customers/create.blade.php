@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Add New Customer</h1>
        <a href="{{ route('customers.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
            <i class="fas fa-arrow-left mr-2"></i>Back to Customers
        </a>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('customers.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-theme-color" required>
                @error('name')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Type <span class="text-red-500">*</span></label>
                <select name="type" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-theme-color" required>
                    <option value="">Select Type</option>
                    <option value="individual" {{ old('type') == 'individual' ? 'selected' : '' }}>Individual</option>
                    <option value="company" {{ old('type') == 'company' ? 'selected' : '' }}>Company</option>
                </select>
                @error('type')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-theme-color">
                @error('email')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-theme-color">
                @error('phone')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Address</label>
                <textarea name="address" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-theme-color">{{ old('address') }}</textarea>
                @error('address')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-theme-color text-white px-6 py-2 rounded-lg hover:bg-theme-hover">Create Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection 