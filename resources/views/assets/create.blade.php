@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w mx-auto">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold mb-8 text-gray-800">Create New Asset</h2>

            <form action="{{ route('assets.store') }}" method="POST" class="space-y-8">
            @csrf

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
                                placeholder="Enter asset name">
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
                                placeholder="Auto-generated">
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
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
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
                                    <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->name }}</option>
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
                                placeholder="Enter location">
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
                                placeholder="Enter description"></textarea>
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
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out">
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
                                placeholder="0.00">
                        </div>
                        @error('purchase_price')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        </div>

                    <!-- Current Value -->
                        <div>
                        <label for="current_value" class="block text-sm font-medium text-gray-700 mb-2">Current Value</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-dollar-sign text-gray-400"></i>
                            </div>
                            <input type="number" name="current_value" id="current_value" step="0.01" required
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                placeholder="0.00">
                        </div>
                        @error('current_value')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        </div>

                    <!-- Supplier -->
                        <div>
                        <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-truck text-gray-400"></i>
                                </div>
                            <select name="supplier_id" id="supplier_id" required
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out">
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('supplier_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    <!-- Tax Group -->
                        <div>
                        <label for="tax_group_id" class="block text-sm font-medium text-gray-700 mb-2">Tax Group</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-percentage text-gray-400"></i>
                            </div>
                            <select name="tax_group_id" id="tax_group_id" required
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out">
                                <option value="">Select Tax Group</option>
                                @foreach($taxGroups as $taxGroup)
                                    <option value="{{ $taxGroup->id }}">{{ $taxGroup->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('tax_group_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
            </div>

                    <!-- Warranty Period -->
                        <div>
                        <label for="warranty_period" class="block text-sm font-medium text-gray-700 mb-2">Warranty Period (Months)</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-shield-alt text-gray-400"></i>
                            </div>
                            <input type="number" name="warranty_period" id="warranty_period" min="0"
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                placeholder="Enter warranty period in months">
                        </div>
                        @error('warranty_period')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    <!-- Warranty Expiry -->
                        <div>
                        <label for="warranty_expiry" class="block text-sm font-medium text-gray-700 mb-2">Warranty Expiry (Optional if period is set)</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-check text-gray-400"></i>
                            </div>
                            <input type="date" name="warranty_expiry" id="warranty_expiry"
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out">
                        </div>
                        @error('warranty_expiry')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                        </div>

                <!-- Depreciation Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Depreciation Method -->
                    <div>
                        <label for="depreciation_method" class="block text-sm font-medium text-gray-700 mb-2">Depreciation Method</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-calculator text-gray-400"></i>
                            </div>
                            <select name="depreciation_method" id="depreciation_method" required
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out">
                                <option value="">Select Method</option>
                                @foreach($depreciationMethods as $key => $value)
                                    <option value="{{ $key }}" {{ old('depreciation_method') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('depreciation_method')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Depreciation Rate -->
                    <div>
                        <label for="depreciation_rate" class="block text-sm font-medium text-gray-700 mb-2">Depreciation Rate (%)</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-percent text-gray-400"></i>
                            </div>
                            <input type="number" name="depreciation_rate" id="depreciation_rate" step="0.01" min="0" max="100" required
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                placeholder="0.00"
                                value="{{ old('depreciation_rate') }}">
                        </div>
                        @error('depreciation_rate')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Useful Life -->
                    <div>
                        <label for="useful_life" class="block text-sm font-medium text-gray-700 mb-2">Useful Life (Years)</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-clock text-gray-400"></i>
                            </div>
                            <input type="number" name="useful_life" id="useful_life" min="1" required
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                placeholder="Enter years"
                                value="{{ old('useful_life') }}">
                        </div>
                        @error('useful_life')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Residual Value -->
                    <div>
                        <label for="residual_value" class="block text-sm font-medium text-gray-700 mb-2">Residual Value</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-dollar-sign text-gray-400"></i>
                            </div>
                            <input type="number" name="residual_value" id="residual_value" step="0.01" min="0" required
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                placeholder="0.00"
                                value="{{ old('residual_value', 0) }}">
                        </div>
                        @error('residual_value')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Depreciation Start Date -->
                    <div>
                        <label for="depreciation_start_date" class="block text-sm font-medium text-gray-700 mb-2">Depreciation Start Date</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-calendar text-gray-400"></i>
                            </div>
                            <input type="date" name="depreciation_start_date" id="depreciation_start_date" required
                                class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                value="{{ old('depreciation_start_date') }}">
                        </div>
                        @error('depreciation_start_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-toggle-on text-gray-400"></i>
                        </div>
                        <select name="status" id="status" required
                            class="h-12 block w-full pl-12 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="disposed">Disposed</option>
                        </select>
                    </div>
                    @error('status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <div class="mt-1">
                        <textarea name="notes" id="notes" rows="3"
                            class="block w-full px-4 py-3 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                            placeholder="Enter any additional notes"></textarea>
                    </div>
                    @error('notes')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
            <div class="flex justify-end space-x-3">
                    <a href="{{ route('assets.index') }}" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1b758c]">
                    Cancel
                </a>
                    <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white !bg-[#1b758c] hover:!bg-[#155c70] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1b758c]">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Save Asset
                </button>
            </div>
        </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    console.log('jQuery ready');
    
    // Function to handle category change
    function handleCategoryChange() {
        console.log('Category change function called');
        
        const categorySelect = $('#category_id');
        const codeInput = $('#code');
        
        if (!categorySelect.length || !codeInput.length) {
            console.error('Required elements not found');
            return;
        }
        
        // Get selected category
        const selectedOption = categorySelect.find('option:selected');
        if (!selectedOption.length || !selectedOption.val()) {
            console.log('No category selected');
            codeInput.val('');
            codeInput.addClass('text-gray-500');
            codeInput.removeClass('text-gray-900');
            return;
        }
        
        // Generate code
        const categoryName = selectedOption.text().trim();
        console.log('Category name:', categoryName);
        
        if (!categoryName) {
            console.error('Empty category name');
            return;
        }

        // Get first three letters, remove any non-alphabetic characters
        const prefix = categoryName.replace(/[^a-zA-Z]/g, '').substring(0, 3).toUpperCase();
        console.log('Prefix:', prefix);
        
        if (prefix.length < 3) {
            console.error('Invalid prefix generated');
            return;
        }
        
        // Generate a random number between 1 and 999
        const number = Math.floor(Math.random() * 999) + 1;
        const code = `${prefix}-${number.toString().padStart(3, '0')}`;
        
        // Update input
        codeInput.val(code);
        codeInput.removeClass('text-gray-500');
        codeInput.addClass('text-gray-900');
        console.log('Code generated:', code);
    }
    
    // Add change event listener
    $('#category_id').on('change', handleCategoryChange);
    
    // Trigger for pre-selected value
    if ($('#category_id').val()) {
        console.log('Pre-selected category found');
        handleCategoryChange();
    }

    // Debug form submission
    $('form').on('submit', function(e) {
        console.log('Form submission started');
        
        // Log all form data
        const formData = new FormData(this);
        console.log('Form data:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        // Check required fields
        const requiredFields = ['name', 'category_id', 'location', 'purchase_date', 'purchase_price', 'current_value', 'supplier_id', 'tax_group_id', 'depreciation_method', 'depreciation_rate', 'useful_life', 'status'];
        let missingFields = [];
        
        requiredFields.forEach(field => {
            if (!formData.get(field)) {
                missingFields.push(field);
            }
        });

        if (missingFields.length > 0) {
            console.error('Missing required fields:', missingFields);
            e.preventDefault();
            alert('Please fill in all required fields: ' + missingFields.join(', '));
            return false;
        }

        // Check if category is selected
        const categoryId = formData.get('category_id');
        if (!categoryId) {
            console.error('No category selected');
            e.preventDefault();
            alert('Please select a category');
            return false;
        }

        // Check if code is generated
        const code = formData.get('code');
        if (!code) {
            console.error('No code generated');
            e.preventDefault();
            alert('Please select a category to generate the asset code');
            return false;
        }

        // Check if purchase price is valid
        const purchasePrice = formData.get('purchase_price');
        if (isNaN(purchasePrice) || parseFloat(purchasePrice) <= 0) {
            console.error('Invalid purchase price');
            e.preventDefault();
            alert('Please enter a valid purchase price');
            return false;
        }

        // Check if current value is valid
        const currentValue = formData.get('current_value');
        if (isNaN(currentValue) || parseFloat(currentValue) <= 0) {
            console.error('Invalid current value');
            e.preventDefault();
            alert('Please enter a valid current value');
            return false;
        }

        console.log('Form validation passed, submitting...');
    });
});
</script>
@endpush 