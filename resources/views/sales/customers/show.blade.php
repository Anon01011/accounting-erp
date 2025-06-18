@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex items-center space-x-4 mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0zM19.071 4.929a10 10 0 11-14.142 0 10 10 0 0114.142 0z" />
        </svg>
        <h1 class="text-3xl font-semibold text-gray-700">Customer Details</h1>
    </div>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
            <p class="text-gray-900">{{ $customer->name }}</p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
            <p class="text-gray-900">{{ $customer->email }}</p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Phone</label>
            <p class="text-gray-900">{{ $customer->phone }}</p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Address</label>
            <p class="text-gray-900 whitespace-pre-line">{{ $customer->address }}</p>
        </div>

        <a href="{{ route('sales.customers.index') }}" class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded">
            Back to List
        </a>
    </div>
</div>
@endsection
