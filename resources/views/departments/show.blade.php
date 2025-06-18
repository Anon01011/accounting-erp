@extends('layouts.dashboard')

@section('content')
<div class="py-4">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Department Details</h1>

            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Name</h2>
                <p class="text-gray-700">{{ $department->name }}</p>
            </div>

            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Code</h2>
                <p class="text-gray-700">{{ $department->code }}</p>
            </div>

            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Status</h2>
                <p class="text-gray-700">{{ $department->status ? 'Active' : 'Inactive' }}</p>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('departments.index') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-gray-800 font-semibold">Back to List</a>
                <a href="{{ route('departments.edit', $department) }}" class="px-4 py-2 bg-[#01657F] rounded text-white font-semibold hover:bg-[#014d61]">Edit Department</a>
            </div>
        </div>
    </div>
</div>
@endsection
