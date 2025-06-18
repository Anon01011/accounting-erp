@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-semibold text-gray-700 mb-4">View Item Category</h1>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
            <p>{{ $itemCategory->name }}</p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Description:</label>
            <p>{{ $itemCategory->description }}</p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Active:</label>
            <p>{{ $itemCategory->is_active ? 'Yes' : 'No' }}</p>
        </div>

        <a href="{{ route('item-categories.index') }}" class="btn btn-secondary">Back to List</a>
        <a href="{{ route('item-categories.edit', $itemCategory) }}" class="btn btn-primary ml-2">Edit</a>
    </div>
</div>
@endsection
