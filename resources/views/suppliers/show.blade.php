@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-semibold text-gray-700 mb-4">Supplier Details</h1>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
            <p class="text-gray-900">{{ $supplier->name }}</p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
            <p class="text-gray-900">{{ $supplier->email }}</p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Phone</label>
            <p class="text-gray-900">{{ $supplier->phone }}</p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Address</label>
            <p class="text-gray-900">{{ $supplier->address }}</p>
        </div>

        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Back to List</a>
    </div>
</div>
@endsection
