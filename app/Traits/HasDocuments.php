<?php

namespace App\Traits;

use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HasDocuments
{
    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function attachDocument(UploadedFile $file, string $description = null)
    {
        $path = $file->store('documents/' . $this->getTable());
        
        return $this->documents()->create([
            'name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'user_id' => auth()->id(),
            'description' => $description
        ]);
    }

    public function detachDocument(Document $document)
    {
        if ($document->documentable_id === $this->id && $document->documentable_type === get_class($this)) {
            Storage::delete($document->file_path);
            return $document->delete();
        }
        return false;
    }

    public function getDocumentUrl(Document $document)
    {
        return Storage::url($document->file_path);
    }
} 