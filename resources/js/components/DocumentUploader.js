export default class DocumentUploader {
    constructor(element) {
        this.element = element;
        this.modelType = element.dataset.modelType;
        this.modelId = element.dataset.modelId;
        this.documents = JSON.parse(element.dataset.documents || '[]');
        this.uploading = false;
        this.uploadError = null;
        this.description = '';

        this.init();
    }

    init() {
        this.render();
        this.attachEventListeners();
    }

    render() {
        this.element.innerHTML = `
            <div class="space-y-4">
                <!-- Upload Form -->
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-medium mb-4">Upload Document</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <input 
                                type="text" 
                                id="document-description"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Enter document description"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">File</label>
                            <input 
                                type="file" 
                                id="document-file"
                                class="mt-1 block w-full"
                            >
                        </div>

                        <div id="upload-error" class="text-red-600 text-sm hidden"></div>

                        <div id="uploading-message" class="text-sm text-gray-500 hidden">
                            Uploading...
                        </div>
                    </div>
                </div>

                <!-- Documents List -->
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-medium mb-4">Attached Documents</h3>
                    
                    <div id="no-documents" class="text-gray-500 text-sm ${this.documents.length === 0 ? '' : 'hidden'}">
                        No documents attached
                    </div>

                    <div id="documents-list" class="space-y-2 ${this.documents.length === 0 ? 'hidden' : ''}">
                        ${this.documents.map(document => this.renderDocument(document)).join('')}
                    </div>
                </div>
            </div>
        `;
    }

    renderDocument(document) {
        return `
            <div class="flex items-center justify-between p-2 bg-gray-50 rounded" data-document-id="${document.id}">
                <div class="flex items-center space-x-4">
                    <div class="text-sm">
                        <div class="font-medium">${document.name}</div>
                        <div class="text-gray-500">${document.description || ''}</div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2">
                    <button 
                        class="download-document text-blue-600 hover:text-blue-800"
                        data-document-id="${document.id}"
                    >
                        Download
                    </button>
                    <button 
                        class="delete-document text-red-600 hover:text-red-800"
                        data-document-id="${document.id}"
                    >
                        Delete
                    </button>
                </div>
            </div>
        `;
    }

    attachEventListeners() {
        const fileInput = this.element.querySelector('#document-file');
        const descriptionInput = this.element.querySelector('#document-description');
        const documentsList = this.element.querySelector('#documents-list');

        fileInput.addEventListener('change', (event) => this.uploadFile(event));
        descriptionInput.addEventListener('input', (event) => {
            this.description = event.target.value;
        });

        documentsList.addEventListener('click', (event) => {
            const documentId = event.target.dataset.documentId;
            if (!documentId) return;

            if (event.target.classList.contains('download-document')) {
                this.downloadDocument(documentId);
            } else if (event.target.classList.contains('delete-document')) {
                this.deleteDocument(documentId);
            }
        });
    }

    async uploadFile(event) {
        const file = event.target.files[0];
        if (!file) return;

        this.setUploading(true);
        this.setError(null);

        const formData = new FormData();
        formData.append('file', file);
        formData.append('documentable_type', this.modelType);
        formData.append('documentable_id', this.modelId);
        formData.append('description', this.description);

        try {
            const response = await axios.post('/documents', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            this.documents.push(response.data.document);
            this.updateDocumentsList();
            this.description = '';
            this.element.querySelector('#document-description').value = '';
            this.element.querySelector('#document-file').value = '';
        } catch (error) {
            this.setError(error.response?.data?.message || 'Failed to upload document');
        } finally {
            this.setUploading(false);
        }
    }

    async deleteDocument(documentId) {
        if (!confirm('Are you sure you want to delete this document?')) return;

        try {
            await axios.delete(`/documents/${documentId}`);
            this.documents = this.documents.filter(d => d.id !== documentId);
            this.updateDocumentsList();
        } catch (error) {
            alert('Failed to delete document');
        }
    }

    downloadDocument(documentId) {
        window.location.href = `/documents/${documentId}/download`;
    }

    updateDocumentsList() {
        const noDocuments = this.element.querySelector('#no-documents');
        const documentsList = this.element.querySelector('#documents-list');

        if (this.documents.length === 0) {
            noDocuments.classList.remove('hidden');
            documentsList.classList.add('hidden');
        } else {
            noDocuments.classList.add('hidden');
            documentsList.classList.remove('hidden');
            documentsList.innerHTML = this.documents.map(document => this.renderDocument(document)).join('');
        }
    }

    setUploading(uploading) {
        this.uploading = uploading;
        const uploadingMessage = this.element.querySelector('#uploading-message');
        uploadingMessage.classList.toggle('hidden', !uploading);
    }

    setError(error) {
        this.uploadError = error;
        const errorElement = this.element.querySelector('#upload-error');
        errorElement.textContent = error || '';
        errorElement.classList.toggle('hidden', !error);
    }
}