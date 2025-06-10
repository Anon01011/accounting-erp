@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Assets Report</h1>
        <div class="flex space-x-2">
            <a href="{{ route('assets.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                Back to Assets
            </a>
            <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                Print Report
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <h2 class="text-xl font-semibold mb-4">Assets Summary</h2>
            
            @foreach($report as $groupCode => $groupAssets)
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-700 mb-2">
                        {{ $groups[$groupCode] ?? 'Unknown Group' }}
                    </h3>
                    
                    @foreach($groupAssets as $classCode => $classAssets)
                        <div class="ml-4 mb-4">
                            <h4 class="text-md font-medium text-gray-600 mb-2">
                                {{ $classes[$groupCode][$classCode] ?? 'Unknown Class' }}
                            </h4>
                            
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Code</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($classAssets as $asset)
                                        <tr class="hover:bg-blue-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-gray-800">{{ $asset->full_account_code }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ $asset->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $asset->description }}</td>
                                            <td class="px-6 py-4 text-center">
                                                @if($asset->is_active)
                                                    <span class="inline-block px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">Active</span>
                                                @else
                                                    <span class="inline-block px-2 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded-full">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                                {{ number_format($asset->balance, 2) }}
                                            </td>
                                        </tr>
                                        
                                        @if($asset->children)
                                            @foreach($asset->children as $child)
                                                <tr class="hover:bg-blue-50 transition">
                                                    <td class="px-6 py-4 pl-12 font-mono text-sm text-gray-800">{{ $child->full_account_code }}</td>
                                                    <td class="px-6 py-4 text-gray-900">{{ $child->name }}</td>
                                                    <td class="px-6 py-4 text-gray-500">{{ $child->description }}</td>
                                                    <td class="px-6 py-4 text-center">
                                                        @if($child->is_active)
                                                            <span class="inline-block px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">Active</span>
                                                        @else
                                                            <span class="inline-block px-2 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded-full">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                                        {{ number_format($child->balance, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        .container {
            width: 100%;
            max-width: none;
        }
        button, a {
            display: none !important;
        }
    }
</style>
@endpush
@endsection 