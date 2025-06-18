@extends('layouts.dashboard')

@section('content')
<div class="py-2">
    <div class="max-w mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h1 class="text-3xl font-semibold text-gray-800 mb-6">Bank Details</h1>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-700 mb-4">Basic Information</h2>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Name</dt>
                                <dd class="mt-1 text-lg text-gray-900">{{ $bank->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Code</dt>
                                <dd class="mt-1 text-lg text-gray-900">{{ $bank->code }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Branch</dt>
                                <dd class="mt-1 text-lg text-gray-900">{{ $bank->branch }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Contact Person</dt>
                                <dd class="mt-1 text-lg text-gray-900">{{ $bank->contact_person }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                <dd class="mt-1 text-lg text-gray-900">{{ $bank->phone }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-lg text-gray-900">{{ $bank->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full {{ $bank->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $bank->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Notes</dt>
                                <dd class="mt-1 text-gray-700">{{ $bank->notes ?? 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h2 class="text-xl font-semibold text-gray-700 mb-4">Additional Information</h2>
                        <p class="text-gray-600">You can add more details or related information here as needed.</p>
                    </div>
                </div>

                <div class="mt-6 flex space-x-4">
                    <a href="{{ route('banks.index') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-gray-800 font-semibold">Back to List</a>
                    <a href="{{ route('banks.edit', $bank) }}" class="px-4 py-2 bg-blue-600 rounded hover:bg-blue-700 text-white font-semibold">Edit Bank</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
