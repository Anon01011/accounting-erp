<!-- Maintenance Modal -->
<div class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true" id="maintenanceModal">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeMaintenanceModal()"></div>
        
        <!-- Modal Panel -->
        <div class="relative inline-block align-middle bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <form action="{{ route('assets.maintenance', $asset) }}" method="POST" class="bg-white">
                @csrf
                <div class="px-8 pt-6 pb-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-tools text-[#01657F] mr-3"></i>
                            Add Maintenance Record
                        </h3>
                        <button type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none" onclick="closeMaintenanceModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="px-8 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div>
                                <label for="maintenance_type" class="block text-sm font-medium text-gray-700 mb-1">Maintenance Type</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-tools text-gray-400"></i>
                                    </div>
                                    <select name="maintenance_type" id="maintenance_type" required
                                            class="h-10 block w-full pl-10 pr-3 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out">
                                        <option value="preventive">Preventive Maintenance</option>
                                        <option value="corrective">Corrective Maintenance</option>
                                        <option value="predictive">Predictive Maintenance</option>
                                        <option value="condition_based">Condition Based Maintenance</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="maintenance_date" class="block text-sm font-medium text-gray-700 mb-1">Maintenance Date</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar text-gray-400"></i>
                                    </div>
                                    <input type="date" name="maintenance_date" id="maintenance_date" required
                                           class="h-10 block w-full pl-10 pr-3 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out">
                                </div>
                            </div>
                        </div>
                        <!-- Right Column -->
                        <div class="space-y-4">
                            <div>
                                <label for="cost" class="block text-sm font-medium text-gray-700 mb-1">Cost</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-dollar-sign text-gray-400"></i>
                                    </div>
                                    <input type="number" name="cost" id="cost" step="0.01" required
                                           class="h-10 block w-full pl-10 pr-3 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                           placeholder="0.00">
                                </div>
                            </div>
                            <div>
                                <label for="next_maintenance_date" class="block text-sm font-medium text-gray-700 mb-1">Next Maintenance Date</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar-alt text-gray-400"></i>
                                    </div>
                                    <input type="date" name="next_maintenance_date" id="next_maintenance_date" required
                                           class="h-10 block w-full pl-10 pr-3 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Full-width fields below the grid -->
                    <div class="space-y-4 mt-6">
                        <div>
                            <label for="performed_by" class="block text-sm font-medium text-gray-700 mb-1">Performed By</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input type="text" name="performed_by" id="performed_by" required
                                       class="h-10 block w-full pl-10 pr-3 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                       placeholder="Enter name or company">
                            </div>
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                                    <i class="fas fa-align-left text-gray-400"></i>
                                </div>
                                <textarea name="description" id="description" rows="3" required
                                          class="block w-full pl-10 pr-3 py-2 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                          placeholder="Enter maintenance details"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-end space-x-4">
                    <button type="button" onclick="closeMaintenanceModal()"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#01657F] transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#01657F] hover:bg-[#01546A] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#01657F] transition-colors duration-200">
                        Add Maintenance Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openMaintenanceModal() {
    const modal = document.getElementById('maintenanceModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeMaintenanceModal() {
    const modal = document.getElementById('maintenanceModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

document.addEventListener('click', function(event) {
    const modal = document.getElementById('maintenanceModal');
    if (event.target === modal) {
        closeMaintenanceModal();
    }
});
</script> 