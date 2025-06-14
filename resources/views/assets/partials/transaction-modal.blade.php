<!-- Transaction Modal -->
<div class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true" id="transactionModal">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeTransactionModal()"></div>
        
        <!-- Modal Panel -->
        <div class="relative inline-block align-middle bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <form action="{{ route('assets.transactions', $asset) }}" method="POST" class="bg-white">
                @csrf
                <div class="px-8 pt-6 pb-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-exchange-alt text-[#01657F] mr-3"></i>
                            Add Transaction
                        </h3>
                        <button type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none" onclick="closeTransactionModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="px-8 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <div>
                                <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-2">Transaction Date</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar text-gray-400"></i>
                                    </div>
                                    <input type="date" name="date" id="transaction_date" required
                                           class="h-10 block w-full pl-10 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out">
                                </div>
                            </div>
                            <div>
                                <label for="transaction_type" class="block text-sm font-medium text-gray-700 mb-2">Transaction Type</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-exchange-alt text-gray-400"></i>
                                    </div>
                                    <select name="type" id="transaction_type" required
                                            class="h-10 block w-full pl-10 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out">
                                        <option value="purchase">Purchase</option>
                                        <option value="sale">Sale</option>
                                        <option value="depreciation">Depreciation</option>
                                        <option value="revaluation">Revaluation</option>
                                        <option value="impairment">Impairment</option>
                                        <option value="disposal">Disposal</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            
                        </div>
                        <!-- Right Column: Purchase Type -->
                        <div class="space-y-6">
                            <div>
                                <label for="purchase_type" class="block text-sm font-medium text-gray-700 mb-2">Purchase Type</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-tag text-gray-400"></i>
                                    </div>
                                    <input type="text" name="purchase_type" id="purchase_type"
                                           class="h-10 block w-full pl-10 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                           placeholder="e.g., Cash, Credit">
                                </div>
                            </div>
                            <div>
                                <label for="transaction_amount" class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-dollar-sign text-gray-400"></i>
                                    </div>
                                    <input type="number" name="amount" id="transaction_amount" step="0.01" required
                                           class="h-10 block w-full pl-10 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                           placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Full-width fields below the grid -->
                    <div class="space-y-6 mt-6">
                        <div>
                            <label for="transaction_description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                                    <i class="fas fa-align-left text-gray-400"></i>
                                </div>
                                <textarea name="description" id="transaction_description" rows="3" required
                                          class="block w-full pl-10 pr-4 py-2.5 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                          placeholder="Enter transaction details"></textarea>
                            </div>
                        </div>
                        <div>
                            <label for="transaction_reference" class="block text-sm font-medium text-gray-700 mb-2">Reference</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-hashtag text-gray-400"></i>
                                </div>
                                <input type="text" name="reference" id="transaction_reference"
                                       class="h-10 block w-full pl-10 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                       placeholder="e.g., Invoice #123, PO #456">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-end space-x-4">
                    <button type="button" onclick="closeTransactionModal()"
                            class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#01657F] transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#01657F] hover:bg-[#01546A] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#01657F] transition-colors duration-200">
                        Add Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openTransactionModal() {
    const modal = document.getElementById('transactionModal');
    modal.classList.remove('hidden');
    // Prevent body scrolling when modal is open
    document.body.style.overflow = 'hidden';
}

function closeTransactionModal() {
    const modal = document.getElementById('transactionModal');
    modal.classList.add('hidden');
    // Restore body scrolling when modal is closed
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('transactionModal');
    if (event.target === modal) {
        closeTransactionModal();
    }
});
</script>
