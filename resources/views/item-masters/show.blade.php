@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-semibold text-gray-700 mb-4">View Item</h1>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
            <p>{{ $itemMaster->name }}</p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Category:</label>
            <p>{{ $itemMaster->category->name ?? '' }}</p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Description:</label>
            <p>{{ $itemMaster->description }}</p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Price:</label>
            <p>{{ number_format($itemMaster->price, 2) }}</p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Active:</label>
            <p>{{ $itemMaster->is_active ? 'Yes' : 'No' }}</p>
        </div>

        <a href="{{ route('item-masters.index') }}" class="btn btn-secondary">Back to List</a>
        <a href="{{ route('item-masters.edit', $itemMaster) }}" class="btn btn-primary ml-2">Edit</a>
    </div>
</div>
@endsection
