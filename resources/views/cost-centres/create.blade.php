@extends('layouts.dashboard')

@section('content')
<div class="py-4">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg p-8">
            <h1 class="text-3xl font-semibold text-gray-800 mb-8">Add New Cost Centre</h1>

            @if ($errors->any())
                <div class="mb-6">
                    <ul class="list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('cost-centres.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-gray-700 font-semibold mb-2">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#01657F] focus:border-transparent">
                </div>

                <div>
                    <label for="code" class="block text-gray-700 font-semibold mb-2">Code</label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#01657F] focus:border-transparent">
                </div>

                <div>
                    <label for="is_active" class="inline-flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked
                               class="form-checkbox h-6 w-6 text-[#01657F]">
                        <span class="ml-3 text-gray-700 font-semibold">Active</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('cost-centres.index') }}" class="px-6 py-3 bg-gray-300 rounded hover:bg-gray-400 text-gray-800 font-semibold">Cancel</a>
                    <button type="submit" class="px-6 py-3 bg-[#01657F] rounded text-white font-semibold hover:bg-[#014d61]">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
