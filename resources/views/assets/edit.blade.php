@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w mx-auto">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold mb-8 text-gray-800">Edit Asset</h2>

            <form action="{{ route('assets.update', $asset) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Asset Name -->
                        <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Asset Name</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-tag text-gray-400"></i>
                            </div>
                            <input type="text" name="name" id="name" required
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                value="{{ old('name', $asset->name) }}">
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        </div>

                    <!-- Asset Code -->
                        <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Asset Code</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-barcode text-gray-400"></i>
                            </div>
                            <input type="text" name="code" id="code" readonly
                                class="h-12 bg-gray-50 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] text-gray-500 transition duration-150 ease-in-out"
                                value="{{ old('code', $asset->code) }}">
                        </div>
                        @error('code')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        </div>

                    <!-- Category -->
                        <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-folder text-gray-400"></i>
                                </div>
                            <select name="category_id" id="category_id" required
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out">
                                <option value="">Select Category</option>
                                @foreach($assetCategories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $asset->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('category_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    <!-- Chart of Account -->
                        <div>
                        <label for="chart_of_account_id" class="block text-sm font-medium text-gray-700 mb-2">Chart of Account</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-book text-gray-400"></i>
                                </div>
                            <select name="chart_of_account_id" id="chart_of_account_id" required
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out">
                                <option value="">Select Chart of Account</option>
                                    @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ old('chart_of_account_id', $asset->chart_of_account_id) == $account->id ? 'selected' : '' }}>{{ $account->account_code }} - {{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('chart_of_account_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    <!-- Location -->
                        <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-map-marker-alt text-gray-400"></i>
                            </div>
                            <input type="text" name="location" id="location" required
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                value="{{ old('location', $asset->location) }}">
                        </div>
                        @error('location')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                                </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <div class="mt-1">
                                <textarea name="description" id="description" rows="3" 
                                class="block w-full px-4 py-3 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                >{{ old('description', $asset->description) }}</textarea>
                        </div>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                </div>
            </div>

                <!-- Purchase Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Purchase Date -->
                        <div>
                        <label for="purchase_date" class="block text-sm font-medium text-gray-700 mb-2">Purchase Date</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-calendar text-gray-400"></i>
                            </div>
                            <input type="date" name="purchase_date" id="purchase_date" required
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                value="{{ old('purchase_date', $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : null) }}">
                        </div>
                        @error('purchase_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    <!-- Purchase Price -->
                        <div>
                        <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-2">Purchase Price</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-dollar-sign text-gray-400"></i>
                            </div>
                            <input type="number" name="purchase_price" id="purchase_price" step="0.01" required
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                value="{{ old('purchase_price', $asset->purchase_price) }}">
                        </div>
                        @error('purchase_price')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    <!-- Acquisition Cost -->
                        <div>
                        <label for="acquisition_cost" class="block text-sm font-medium text-gray-700 mb-2">Acquisition Cost</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-dollar-sign text-gray-400"></i>
                            </div>
                            <input type="number" name="acquisition_cost" id="acquisition_cost" step="0.01" required
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                value="{{ old('acquisition_cost', $asset->acquisition_cost) }}">
                        </div>
                        @error('acquisition_cost')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        </div>

                    <!-- Salvage Value -->
                        <div>
                        <label for="salvage_value" class="block text-sm font-medium text-gray-700 mb-2">Salvage Value</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-dollar-sign text-gray-400"></i>
                            </div>
                            <input type="number" name="salvage_value" id="salvage_value" step="0.01" required
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                value="{{ old('salvage_value', $asset->salvage_value) }}">
                        </div>
                        @error('salvage_value')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                </div>
            </div>

            <!-- Asset Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Serial Number -->
                        <div>
                        <label for="details[serial_number]" class="block text-sm font-medium text-gray-700 mb-2">Serial Number</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-barcode text-gray-400"></i>
                            </div>
                            <input type="text" name="details[serial_number]" id="details[serial_number]"
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                value="{{ old('details.serial_number', $details->serial_number ?? '') }}">
                        </div>
                        @error('details.serial_number')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        </div>

                    <!-- Supplier -->
                        <div>
                        <label for="details[supplier]" class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-truck text-gray-400"></i>
                                </div>
                            <select name="details[supplier]" id="details[supplier]" required
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out">
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->name }}" {{ old('details.supplier', optional($details)->supplier) == $supplier->name ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @error('details.supplier')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    <!-- Warranty Period -->
                        <div>
                        <label for="details[warranty_period]" class="block text-sm font-medium text-gray-700 mb-2">Warranty Period</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-shield-alt text-gray-400"></i>
                            </div>
                            <input type="text" name="details[warranty_period]" id="details[warranty_period]"
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                value="{{ old('details.warranty_period', $details->warranty_period ?? '') }}">
                        </div>
                        @error('details.warranty_period')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    <!-- Warranty Expiry -->
                        <div>
                        <label for="details[warranty_expiry]" class="block text-sm font-medium text-gray-700 mb-2">Warranty Expiry</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-alt text-gray-400"></i>
                            </div>
                            <input type="date" name="details[warranty_expiry]" id="details[warranty_expiry]"
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                value="{{ old('details.warranty_expiry', optional($details)->warranty_expiry ? $details->warranty_expiry->format('Y-m-d') : '') }}">
                        </div>
                        @error('details.warranty_expiry')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    <!-- Depreciation Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Depreciation Method -->
                        <div>
                            <label for="details[depreciation_method]" class="block text-sm font-medium text-gray-700 mb-2">Depreciation Method</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-calculator text-gray-400"></i>
                                </div>
                                <select name="details[depreciation_method]" id="details[depreciation_method]" required
                                    class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out">
                                    <option value="">Select Method</option>
                                    @foreach($depreciationMethods as $key => $value)
                                        <option value="{{ $key }}" {{ old('details.depreciation_method', $details->depreciation_method ?? '') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('details.depreciation_method')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Depreciation Rate -->
                        <div>
                            <label for="details[depreciation_rate]" class="block text-sm font-medium text-gray-700 mb-2">Depreciation Rate (%)</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-percent text-gray-400"></i>
                                </div>
                                <input type="number" name="details[depreciation_rate]" id="details[depreciation_rate]" step="0.01" min="0" max="100" required
                                    class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                    placeholder="0.00"
                                    value="{{ old('details.depreciation_rate', $details->depreciation_rate ?? '') }}">
                            </div>
                            @error('details.depreciation_rate')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Useful Life -->
                        <div>
                            <label for="details[useful_life]" class="block text-sm font-medium text-gray-700 mb-2">Useful Life (Years)</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-clock text-gray-400"></i>
                                </div>
                                <input type="number" name="details[useful_life]" id="details[useful_life]" min="1" required
                                    class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                    placeholder="Enter years"
                                    value="{{ old('details.useful_life', $details->useful_life ?? '') }}">
                            </div>
                            @error('details.useful_life')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Residual Value -->
                        <div>
                            <label for="details[residual_value]" class="block text-sm font-medium text-gray-700 mb-2">Residual Value</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-dollar-sign text-gray-400"></i>
                                </div>
                                <input type="number" name="details[residual_value]" id="details[residual_value]" step="0.01" min="0" required
                                    class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                    placeholder="0.00"
                                    value="{{ old('details.residual_value', $details->residual_value ?? 0) }}">
                            </div>
                            @error('details.residual_value')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Depreciation Start Date -->
                        <div>
                            <label for="details[depreciation_start_date]" class="block text-sm font-medium text-gray-700 mb-2">Depreciation Start Date</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar text-gray-400"></i>
                                </div>
                                <input type="date" name="details[depreciation_start_date]" id="details[depreciation_start_date]" required
                                    class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                    value="{{ old('details.depreciation_start_date', optional($details->depreciation_start_date)->format('Y-m-d') ?? '') }}">
                            </div>
                            @error('details.depreciation_start_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Condition -->
                    <div>
                        <label for="details[condition]" class="block text-sm font-medium text-gray-700 mb-2">Condition</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-tachometer-alt text-gray-400"></i>
                            </div>
                            <select name="details[condition]" id="details[condition]" required
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out">
                                <option value="">Select Condition</option>
                                @foreach($conditions as $key => $value)
                                    <option value="{{ $key }}" {{ old('details.condition', $details->condition ?? '') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('details.condition')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label for="details[notes]" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <div class="mt-1">
                        <textarea name="details[notes]" id="details[notes]" rows="3"
                            class="block w-full px-4 py-3 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                            >{{ old('details.notes', $details->notes ?? '') }}</textarea>
                    </div>
                    @error('details.notes')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
            </div>

                <div class="flex justify-end space-x-4">
                <a href="{{ route('assets.index') }}" 
                        class="inline-flex items-center px-6 py-3 bg-gray-200 border border-transparent rounded-md font-semibold text-base text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to List
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-[#1b758c] hover:bg-[#01657F] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1b758c] transition duration-150 ease-in-out">
                    Update Asset
                </button>
            </div>
        </form>
    </div>
</div>
</div>
@endsection 