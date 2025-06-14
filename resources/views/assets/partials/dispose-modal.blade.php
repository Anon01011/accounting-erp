<!-- Dispose Modal -->
<div class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true" id="disposeModal">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeDisposeModal()"></div>
        
        <!-- Modal Panel -->
        <div class="relative inline-block align-middle bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <form action="{{ route('assets.dispose', $asset) }}" method="POST" class="bg-white">
                @csrf
                <div class="px-8 pt-6 pb-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-trash-alt text-red-600 mr-3"></i>
                            Dispose Asset
                        </h3>
                        <button type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none" onclick="closeDisposeModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="px-8 py-6">
                    <div class="space-y-6">
                        <div>
                            <label for="disposal_date" class="block text-sm font-medium text-gray-700 mb-2">Disposal Date</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar text-gray-400"></i>
                                </div>
                                <input type="date" name="disposal_date" id="disposal_date" required
                                       class="h-10 block w-full pl-10 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out">
                            </div>
                        </div>
                        <div>
                            <label for="disposal_value" class="block text-sm font-medium text-gray-700 mb-2">Disposal Value</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-dollar-sign text-gray-400"></i>
                                </div>
                                <input type="number" name="disposal_value" id="disposal_value" step="0.01" required
                                       class="h-10 block w-full pl-10 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                       placeholder="0.00">
                            </div>
                        </div>
                        <div>
                            <label for="disposal_reason" class="block text-sm font-medium text-gray-700 mb-2">Reason for Disposal</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                                    <i class="fas fa-comment-alt text-gray-400"></i>
                                </div>
                                <textarea name="disposal_reason" id="disposal_reason" rows="4" required
                                          class="block w-full pl-10 pr-4 sm:text-sm rounded-md border border-[#1b758c] focus:ring-[#1b758c] focus:border-[#1b758c] transition duration-150 ease-in-out"
                                          placeholder="Enter reason for disposal"></textarea>
                            </div>
                        </div>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Warning</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>This action cannot be undone. The asset will be marked as disposed and removed from active inventory.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Disposal Logs -->
                        <div class="bg-gray-50 rounded-lg border border-gray-200">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <h4 class="text-sm font-medium text-gray-900 flex items-center">
                                    <i class="fas fa-history text-gray-500 mr-2"></i>
                                    Disposal History
                                </h4>
                            </div>
                            <div class="p-4">
                                @if($asset->disposal_logs && $asset->disposal_logs->count() > 0)
                                    <div class="space-y-4">
                                        @foreach($asset->disposal_logs as $log)
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0">
                                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                                        <i class="fas fa-user text-gray-500 text-sm"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between">
                                                        <p class="text-sm font-medium text-gray-900">
                                                            {{ $log->user->name ?? 'System' }}
                                                        </p>
                                                        <p class="text-xs text-gray-500">
                                                            {{ $log->created_at->format('M d, Y H:i') }}
                                                        </p>
                                                    </div>
                                                    <p class="text-sm text-gray-600 mt-1">
                                                        {{ $log->description }}
                                                    </p>
                                                    <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500">
                                                        <span class="flex items-center">
                                                            <i class="fas fa-calendar-alt mr-1"></i>
                                                            {{ $log->disposal_date->format('Y-m-d') }}
                                                        </span>
                                                        <span class="flex items-center">
                                                            <i class="fas fa-dollar-sign mr-1"></i>
                                                            {{ number_format($log->disposal_value, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <div class="text-gray-400 mb-2">
                                            <i class="fas fa-history text-2xl"></i>
                                        </div>
                                        <p class="text-sm text-gray-500">No disposal history available</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-end space-x-4">
                    <button type="button" onclick="closeDisposeModal()"
                            class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#01657F] transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                        Confirm Disposal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openDisposeModal() {
    const modal = document.getElementById('disposeModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('block');
        // Prevent body scrolling when modal is open
        document.body.style.overflow = 'hidden';
    }
}

function closeDisposeModal() {
    const modal = document.getElementById('disposeModal');
    if (modal) {
        modal.classList.remove('block');
        modal.classList.add('hidden');
        // Restore body scrolling when modal is closed
        document.body.style.overflow = 'auto';
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('disposeModal');
    if (event.target === modal) {
        closeDisposeModal();
    }
});

// Close modal when pressing Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDisposeModal();
    }
});
</script> 