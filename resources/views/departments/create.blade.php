@extends('layouts.dashboard')

@section('content')
<div class="py-4">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Add New Department</h1>

            @if ($errors->any())
                <div class="mb-4">
                    <ul class="list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('departments.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-semibold mb-2">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#01657F] focus:border-transparent">
                </div>

                <div class="mb-4">
                    <label for="code" class="block text-gray-700 font-semibold mb-2">Code</label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#01657F] focus:border-transparent">
                </div>

                <div class="mb-4">
                    <label for="status" class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="status" id="status" value="1" checked
                               class="form-checkbox h-5 w-5 text-[#01657F]">
                        <span class="ml-2 text-gray-700 font-semibold">Active</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('departments.index') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-gray-800 font-semibold">Cancel</a>
                    <button type="submit" class="px-4 py-2 bg-[#01657F] rounded text-white font-semibold hover:bg-[#014d61]">Add Department</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
