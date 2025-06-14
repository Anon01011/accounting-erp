@extends('layouts.dashboard')

@section('content')
<div class="max-w mx-auto px-3 py-4">
    <!-- Asset Header Section -->
    <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between mb-6 flex-wrap gap-6">
            <div class="flex items-center gap-6">
                <div class="bg-gray-50 p-5 rounded-lg flex-shrink-0 border border-gray-200">
                    <i class="fas fa-cube text-4xl text-[#01657F]"></i>
                </div>
                <div class="space-y-2">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $asset->name }}</h1>
                    <p class="text-sm text-gray-600">ID: {{ $asset->code }}</p>
                    <div class="flex items-center space-x-3">
                        @if($asset->is_active)
                            <span class="bg-white text-emerald-700 px-4 py-1.5 rounded-md text-sm font-medium border border-gray-300 flex items-center shadow-sm">
                                <i class="fas fa-circle text-emerald-500 text-xs mr-2"></i> Active
                            </span>
                        @else
                            <span class="bg-white text-red-700 px-4 py-1.5 rounded-md text-sm font-medium border border-gray-300 flex items-center shadow-sm">
                                <i class="fas fa-circle text-red-500 text-xs mr-2"></i> Inactive
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('assets.edit', $asset->id) }}" class="px-5 py-2.5 bg-[#01657F] text-white rounded-md shadow-sm hover:bg-[#01546A] transition-colors duration-200 flex items-center text-sm font-medium">
                    <i class="fas fa-edit mr-3"></i> Edit
                </a>
                <button onclick="openDisposeModal()" class="px-5 py-2.5 bg-red-50 text-red-700 rounded-md shadow-sm hover:bg-red-100 transition-colors duration-200 flex items-center text-sm font-medium border border-red-200">
                    <i class="fas fa-trash-alt mr-3"></i> Dispose
                </button>
            </div>
        </div>
        <p class="text-gray-600 text-sm mt-4">{{ $asset->description }}</p>
    </div>

    <div class="grid grid-cols-12 gap-4">
        <!-- Main Content Column -->
        <div class="col-span-12 lg:col-span-8 space-y-4">
            <!-- Basic Information Card -->
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-4">
                <h2 class="text-base font-semibold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-info-circle text-[#01657F] mr-2"></i> Basic Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded-md p-3">
                        <label class="block text-xs font-medium text-gray-500 mb-0.5">Category</label>
                        <p class="text-gray-900 font-medium text-sm">{{ $asset->category->name ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-md p-3">
                        <label class="block text-xs font-medium text-gray-500 mb-0.5">Condition</label>
                        <span class="px-2 py-0.5 text-xs rounded-md font-medium inline-block
                            @if($asset->details->first()?->condition == 'Good') bg-emerald-50 text-emerald-700 border border-emerald-200
                            @elseif($asset->details->first()?->condition == 'Fair') bg-amber-50 text-amber-700 border border-amber-200
                            @else bg-red-50 text-red-700 border border-red-200 @endif">
                            {{ $asset->details->first()?->condition ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="bg-gray-50 rounded-md p-3">
                        <label class="block text-xs font-medium text-gray-500 mb-0.5">Location</label>
                        <p class="text-gray-900 font-medium text-sm">{{ $asset->location ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-md p-3">
                        <label class="block text-xs font-medium text-gray-500 mb-0.5">Serial Number</label>
                        <p class="text-gray-900 font-medium text-sm">{{ $asset->details->first()?->serial_number ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-md p-3">
                        <label class="block text-xs font-medium text-gray-500 mb-0.5">Supplier</label>
                        <p class="text-gray-900 font-medium text-sm">{{ $asset->supplier->name ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-md p-3">
                        <label class="block text-xs font-medium text-gray-500 mb-0.5">Notes</label>
                        <p class="text-gray-900 font-medium text-sm">{{ $asset->notes ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Financial Information Card -->
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-4">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-base font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-chart-line text-[#01657F] mr-2"></i> Financial Information
                </h2>
                    <button onclick="calculateDepreciation({{ $asset->id }})" class="px-3 py-1.5 bg-[#01657F] text-white rounded-md shadow-sm hover:bg-[#01546A] transition-colors duration-200 flex items-center text-sm font-medium">
                        <i class="fas fa-calculator mr-2"></i> Calculate Depreciation
                    </button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div class="bg-[#01657F]/5 rounded-md p-3 border border-[#01657F]/10">
                        <div class="flex items-center mb-1">
                            <i class="fas fa-dollar-sign text-[#01657F] text-base mr-2"></i>
                            <span class="text-xs font-medium text-gray-700">Current Book Value</span>
                        </div>
                        <p class="text-xl font-bold text-[#01657F]">
                            ${{ number_format($asset->current_value, 2) ?? '0.00' }}
                        </p>
                    </div>
                    <div class="bg-red-50 rounded-md p-3 border border-red-100">
                        <div class="flex items-center mb-1">
                            <i class="fas fa-minus-circle text-red-600 text-base mr-2"></i>
                            <span class="text-xs font-medium text-gray-700">Accumulated Depreciation</span>
                        </div>
                        <p class="text-xl font-bold text-red-600">
                            ${{ number_format($asset->getAccumulatedDepreciation(), 2) }}
                        </p>
                    </div>
                    <div class="bg-purple-50 rounded-md p-3 border border-purple-100">
                        <div class="flex items-center mb-1">
                            <i class="fas fa-calendar-alt text-purple-600 text-base mr-2"></i>
                            <span class="text-xs font-medium text-gray-700">Next Depreciation</span>
                        </div>
                        <p class="text-xl font-bold text-purple-600">
                            {{ $asset->getNextDepreciationDate()->format('Y-m-d') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tabs Section -->
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-4">
                <div class="border-b border-gray-200 mb-3">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button type="button" class="tab-button inline-flex items-center px-3 py-2 border-b-2 font-semibold text-base transition-colors duration-200 focus:outline-none text-gray-600 hover:text-gray-700 hover:border-gray-300" data-tab="transactions">
                            <i class="fas fa-exchange-alt mr-2"></i> Transactions
                        </button>
                        <button type="button" class="tab-button inline-flex items-center px-3 py-2 border-b-2 font-semibold text-base transition-colors duration-200 focus:outline-none text-gray-600 hover:text-gray-700 hover:border-gray-300" data-tab="maintenance">
                            <i class="fas fa-tools mr-2"></i> Maintenance
                        </button>
                        <button type="button" class="tab-button inline-flex items-center px-3 py-2 border-b-2 font-semibold text-base transition-colors duration-200 focus:outline-none text-gray-600 hover:text-gray-700 hover:border-gray-300" data-tab="documents">
                            <i class="fas fa-file-alt mr-2"></i> Documents
                        </button>
                    </nav>
                </div>

                <!-- Tab Contents -->
                <div id="tab-transactions" class="tab-content active-tab mt-3">
                    <div class="flex justify-end mb-3">
                        <button onclick="openTransactionModal()" class="px-3 py-1.5 bg-[#01657F] text-white rounded-md shadow-sm hover:bg-[#01546A] transition-colors duration-200 text-sm font-medium flex items-center">
                            <i class="fas fa-plus mr-2"></i> Add Transaction
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($asset->transactions as $transaction)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $transaction->date->format('Y-m-d') }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ number_format($transaction->amount, 2) }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $transaction->description }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $transaction->reference }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No transactions found for this asset.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="tab-maintenance" class="tab-content hidden mt-3">
                    <div class="flex justify-end mb-3">
                        <button onclick="openMaintenanceModal()" class="px-3 py-1.5 bg-[#01657F] text-white rounded-md shadow-sm hover:bg-[#01546A] transition-colors duration-200 flex items-center text-sm font-medium">
                            <i class="fas fa-plus mr-2"></i> Add Maintenance
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performed By</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Maintenance</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($asset->maintenanceRecords as $record)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $record->maintenance_date->format('Y-m-d') }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $record->maintenance_type }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ number_format($record->cost, 2) }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $record->performed_by }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $record->next_maintenance_date->format('Y-m-d') }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $record->description }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No maintenance records found for this asset.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="tab-documents" class="tab-content hidden mt-3">
                    <div class="flex justify-end mb-3">
                        <button onclick="openDocumentModal()" class="px-3 py-1.5 bg-[#01657F] text-white rounded-md shadow-sm hover:bg-[#01546A] transition-colors duration-200 flex items-center text-sm font-medium">
                            <i class="fas fa-plus mr-2"></i> Upload Document
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded On</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($asset->documents as $document)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $document->name }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($document->type) }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $document->created_at->format('Y-m-d') }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('assets.documents.download', $document->id) }}" class="text-[#01657F] hover:text-[#01546A] mr-2 transition-colors duration-150" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button type="button" onclick="confirmDeleteDocument({{ $document->id }})" class="text-red-600 hover:text-red-900 transition-colors duration-150" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No documents found for this asset.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-span-12 lg:col-span-4 space-y-4">
            <!-- Warranty Status Card -->
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-4">
                <h2 class="text-base font-semibold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-shield-alt text-[#01657F] mr-2"></i> Warranty Status
                </h2>
                @if($asset->isUnderWarranty())
                    <div class="bg-emerald-50 rounded-md p-4 text-center border border-emerald-100">
                        <div class="flex items-center justify-center mb-2">
                            <i class="fas fa-check-circle text-emerald-600 text-xl mr-2"></i>
                            <span class="text-emerald-700 text-base font-semibold">Under Warranty</span>
                        </div>
                        <p class="text-gray-600 text-xs">
                            Expires on
                        </p>
                        <p class="text-gray-900 text-lg font-bold mt-0.5">
                            {{ optional($asset->details->first())->warranty_expiry ? $asset->details->first()->warranty_expiry->format('Y-m-d') : 'N/A' }}
                        </p>
                    </div>
                @else
                    <div class="bg-red-50 rounded-md p-4 text-center border border-red-100">
                        <div class="flex items-center justify-center mb-2">
                            <i class="fas fa-times-circle text-red-600 text-xl mr-2"></i>
                            <span class="text-red-700 text-base font-semibold">Warranty Expired</span>
                        </div>
                        <p class="text-gray-600 text-xs">
                            Expired on
                        </p>
                        <p class="text-gray-900 text-lg font-bold mt-0.5">
                            {{ optional($asset->details->first())->warranty_expiry ? $asset->details->first()->warranty_expiry->format('Y-m-d') : 'N/A' }}
                        </p>
                    </div>
                @endif
            </div>

            <!-- Depreciation Status Card -->
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-4">
                <h2 class="text-base font-semibold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-chart-pie text-[#01657F] mr-2"></i> Depreciation Status
                </h2>
                <div class="space-y-3">
                    @php
                        $depreciationStatus = $asset->getDepreciationStatus();
                        $statusTextClass = '';
                        switch ($depreciationStatus) {
                            case 'Fully Depreciated': $statusTextClass = 'text-red-600'; break;
                            case 'Mostly Depreciated': $statusTextClass = 'text-orange-600'; break;
                            case 'Half Depreciated': $statusTextClass = 'text-yellow-600'; break;
                            case 'Partially Depreciated': $statusTextClass = 'text-emerald-600'; break;
                            default: $statusTextClass = 'text-gray-600'; break;
                        }
                    @endphp
                    <div class="bg-gray-50 rounded-md p-3 border border-gray-100">
                        <p class="text-base font-semibold {{ $statusTextClass }} text-center">{{ $depreciationStatus }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 rounded-md p-3 border border-gray-100">
                            <p class="text-xs text-gray-500 mb-0.5">Accumulated</p>
                            <p class="text-base font-semibold text-gray-900">${{ number_format($asset->getAccumulatedDepreciation(), 2) }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-md p-3 border border-gray-100">
                            <p class="text-xs text-gray-500 mb-0.5">Net Book Value</p>
                            <p class="text-base font-semibold text-gray-900">${{ number_format($asset->getNetBookValue(), 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Key Dates Card -->
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-4">
                <h2 class="text-base font-semibold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-calendar-alt text-[#01657F] mr-2"></i> Key Dates
                </h2>
                <div class="space-y-3">
                    <div class="bg-gray-50 rounded-md p-3 border border-gray-100">
                        <p class="text-xs text-gray-500 mb-0.5">Purchase Date</p>
                        <p class="text-sm font-medium text-gray-900">{{ $asset->purchase_date->format('Y-m-d') }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-md p-3 border border-gray-100">
                        <p class="text-xs text-gray-500 mb-0.5">Depreciation Start</p>
                        <p class="text-sm font-medium text-gray-900">{{ optional($asset->details->first())->depreciation_start_date ? $asset->details->first()->depreciation_start_date->format('Y-m-d') : 'N/A' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-md p-3 border border-gray-100">
                        <p class="text-xs text-gray-500 mb-0.5">Depreciation End</p>
                        <p class="text-sm font-medium text-gray-900">{{ optional($asset->details->first())->depreciation_end_date ? $asset->details->first()->depreciation_end_date->format('Y-m-d') : 'N/A' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-md p-3 border border-gray-100">
                        <p class="text-xs text-gray-500 mb-0.5">Last Maintenance</p>
                        <p class="text-sm font-medium text-gray-900">{{ optional($asset->maintenanceRecords()->latest()->first())->maintenance_date ? $asset->maintenanceRecords()->latest()->first()->maintenance_date->format('Y-m-d') : 'N/A' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-md p-3 border border-gray-100">
                        <p class="text-xs text-gray-500 mb-0.5">Last Revaluation</p>
                        <p class="text-sm font-medium text-gray-900">{{ optional($asset->getLastRevaluation())->revaluation_date ? $asset->getLastRevaluation()->revaluation_date->format('Y-m-d') : 'N/A' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-md p-3 border border-gray-100">
                        <p class="text-xs text-gray-500 mb-0.5">Last Impairment</p>
                        <p class="text-sm font-medium text-gray-900">{{ optional($asset->getLastImpairment())->impairment_date ? $asset->getLastImpairment()->impairment_date->format('Y-m-d') : 'N/A' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-md p-3 border border-gray-100">
                        <p class="text-xs text-gray-500 mb-0.5">Disposal Date</p>
                        <p class="text-sm font-medium text-gray-900">{{ optional($asset)->disposal_date ? $asset->disposal_date->format('Y-m-d') : 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modals --}}
@include('assets.partials.maintenance-modal')
@include('assets.partials.document-modal')
@include('assets.partials.dispose-modal')
@include('assets.partials.transaction-modal')

@endsection

@push('scripts')
<script>
    // Tab switching logic
    document.addEventListener('DOMContentLoaded', function () {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        // Initially set the active tab
        document.querySelector('.tab-button[data-tab="transactions"]').classList.add('border-[#01657F]', 'text-[#01657F]');
        document.getElementById('tab-transactions').classList.remove('hidden');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Deactivate all tab buttons and hide all tab contents
                tabButtons.forEach(btn => {
                    btn.classList.remove('border-[#01657F]', 'text-[#01657F]');
                    btn.classList.add('text-gray-600', 'hover:text-gray-700', 'hover:border-gray-300');
                });
                tabContents.forEach(content => {
                    content.classList.remove('active-tab');
                    content.classList.add('hidden');
                });

                // Activate the clicked tab button and show its content
                button.classList.add('border-[#01657F]', 'text-[#01657F]');
                button.classList.remove('text-gray-600', 'hover:text-gray-700', 'hover:border-gray-300');
                const targetTab = button.dataset.tab;
                document.getElementById(`tab-${targetTab}`).classList.remove('hidden');
                document.getElementById(`tab-${targetTab}`).classList.add('active-tab');
            });
        });
    });

    // Modals JS
    function openMaintenanceModal() {
        const modal = document.getElementById('maintenanceModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('block');
        }
    }

    function closeMaintenanceModal() {
        const modal = document.getElementById('maintenanceModal');
        if (modal) {
            modal.classList.remove('block');
            modal.classList.add('hidden');
        }
    }

    function openDocumentModal() {
        const modal = document.getElementById('documentModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('block');
        }
    }

    function closeDocumentModal() {
        const modal = document.getElementById('documentModal');
        if (modal) {
            modal.classList.remove('block');
            modal.classList.add('hidden');
        }
    }

    function openDisposeModal() {
        const modal = document.getElementById('disposeModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('block'); // Ensure it becomes block to be visible
        }
    }

    function closeDisposeModal() {
        const modal = document.getElementById('disposeModal');
        if (modal) {
            modal.classList.remove('block');
            modal.classList.add('hidden');
        }
    }

    // New Transaction Modal Functions
    function openTransactionModal() {
        const modal = document.getElementById('transactionModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('block');
        }
    }

    function closeTransactionModal() {
        const modal = document.getElementById('transactionModal');
        if (modal) {
            modal.classList.remove('block');
            modal.classList.add('hidden');
        }
    }

    function calculateDepreciation(assetId) {
        if (confirm('Do you want to calculate depreciation for this asset?')) {
            fetch(`/assets/${assetId}/depreciation`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Failed to calculate depreciation');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showNotification(
                        `Depreciation calculated successfully:
                        Amount: ${formatCurrency(data.amount)}
                        Current Value: ${formatCurrency(data.current_value)}
                        Accumulated Depreciation: ${formatCurrency(data.accumulated_depreciation)}`,
                        'success'
                    );
                    location.reload();
                } else {
                    showNotification(data.message || 'Failed to calculate depreciation', 'error');
                }
            })
            .catch(error => {
                console.error('Depreciation calculation error:', error);
                showNotification(error.message || 'An error occurred while calculating depreciation', 'error');
            });
        }
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    }

    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg transform transition-transform duration-300 translate-x-full z-50`;
        
        // Set background color based on type
        switch(type) {
            case 'success':
                notification.classList.add('bg-green-500');
                break;
            case 'error':
                notification.classList.add('bg-red-500');
                break;
            case 'warning':
                notification.classList.add('bg-yellow-500');
                break;
            default:
                notification.classList.add('bg-blue-500');
        }
        
        notification.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    ${type === 'success' ? 
                        '<svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' :
                        '<svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
                    }
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-white whitespace-pre-line">${message}</p>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
    }
</script>
@endpush
