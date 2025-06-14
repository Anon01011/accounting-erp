<!-- Document Modal -->
<div class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true" id="documentModal">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <form action="{{ route('assets.documents.store', $asset) }}" method="POST" enctype="multipart/form-data" class="bg-white">
                @csrf
                <div class="px-8 pt-6 pb-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-file-alt text-[#01657F] mr-3"></i>
                            Upload Document
                        </h3>
                        <button type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none" onclick="closeDocumentModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="px-8 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <div>
                                <label for="document_name" class="block text-sm font-medium text-gray-700 mb-2">Document Name</label>
                                <input type="text" name="document_name" id="document_name" required
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50"
                                       placeholder="Enter document name">
                            </div>
                            <div>
                                <label for="document_type" class="block text-sm font-medium text-gray-700 mb-2">Document Type</label>
                                <select name="document_type" id="document_type" required
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#01657F] focus:ring focus:ring-[#01657F] focus:ring-opacity-50">
                                    <option value="invoice">Invoice</option>
                                    <option value="warranty">Warranty</option>
                                    <option value="manual">Manual</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <!-- Right Column -->
                        <div class="space-y-6">
                            <div>
                                <label for="document_file" class="block text-sm font-medium text-gray-700 mb-2">File</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-[#01657F] transition-colors duration-200">
                                    <div class="space-y-1 text-center">
                                        <i class="fas fa-cloud-upload-alt text-[#01657F] text-3xl mb-3"></i>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="document_file" class="relative cursor-pointer bg-white rounded-md font-medium text-[#01657F] hover:text-[#01546A] focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-[#01657F]">
                                                <span>Upload a file</span>
                                                <input id="document_file" name="document_file" type="file" class="sr-only" required>
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PDF, DOC, DOCX, JPG, PNG up to 10MB</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-end space-x-4">
                    <button type="button" onclick="closeDocumentModal()"
                            class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#01657F] transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#01657F] hover:bg-[#01546A] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#01657F] transition-colors duration-200">
                        Upload Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function closeDocumentModal() {
    const modal = document.getElementById('documentModal');
    if (modal) {
        modal.classList.remove('block');
        modal.classList.add('hidden');
    }
}
</script> 