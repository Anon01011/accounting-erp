@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold text-gray-700">GL Control Groups</h1>
        <a href="{{ route('gl-control-groups.create') }}" class="btn btn-primary">Add New</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Code</th>
                    <th class="py-3 px-6 text-left">Name</th>
                    <th class="py-3 px-6 text-left">Description</th>
                    <th class="py-3 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                @forelse($glControlGroups as $glControlGroup)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left whitespace-nowrap">{{ $glControlGroup->code }}</td>
                    <td class="py-3 px-6 text-left">{{ $glControlGroup->name }}</td>
                    <td class="py-3 px-6 text-left">{{ $glControlGroup->description }}</td>
                    <td class="py-3 px-6 text-center">
                        <a href="{{ route('gl-control-groups.show', $glControlGroup) }}" class="text-blue-500 hover:text-blue-700 mr-2">View</a>
                        <a href="{{ route('gl-control-groups.edit', $glControlGroup) }}" class="text-yellow-500 hover:text-yellow-700 mr-2">Edit</a>
                        <form action="{{ route('gl-control-groups.destroy', $glControlGroup) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4">No GL control groups found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $glControlGroups->links() }}
    </div>
</div>
@endsection
