import './bootstrap';
import '../css/app.css';
import DocumentUploader from './components/DocumentUploader';

// Initialize any global JavaScript functionality here
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DocumentUploader components
    const documentUploaders = document.querySelectorAll('[data-document-uploader]');
    documentUploaders.forEach(element => {
        new DocumentUploader(element);
    });
});