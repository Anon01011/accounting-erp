@extends('layouts.dashboard')

@section('content')
<div class="py-4">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Cost Centre Details</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Basic Information</h2>
                    <dl class="space-y-5">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $costCentre->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Code</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $costCentre->code }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full {{ $costCentre->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $costCentre->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Notes</dt>
                            <dd class="mt-1 text-gray-700">{{ $costCentre->notes ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Additional Information</h2>
                    <p class="text-gray-600">You can add more details or related information here as needed.</p>
                </div>
            </div>

            <div class="mt-6 flex space-x-4">
                <a href="{{ route('cost-centres.index') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-gray-800 font-semibold">Back to List</a>
                <a href="{{ route('cost-centres.edit', $costCentre) }}" class="px-4 py-2 bg-blue-600 rounded hover:bg-blue-700 text-white font-semibold">Edit Cost Centre</a>
            </div>
        </div>
    </div>
</div>
@endsection
