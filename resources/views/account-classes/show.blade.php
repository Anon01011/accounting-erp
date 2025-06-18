@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-semibold text-gray-700 mb-4">Account Class Details</h1>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Code</label>
            <p class="text-gray-900">{{ $accountClass->code }}</p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
            <p class="text-gray-900">{{ $accountClass->name }}</p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
            <p class="text-gray-900">{{ $accountClass->description }}</p>
        </div>

        <a href="{{ route('account-classes.index') }}" class="btn btn-secondary">Back to List</a>
    </div>
</div>
@endsection
