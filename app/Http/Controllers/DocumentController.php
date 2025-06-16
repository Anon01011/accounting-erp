<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::with('user')
            ->when($request->documentable_type, function ($q) use ($request) {
                return $q->where('documentable_type', $request->documentable_type);
            })
            ->when($request->date_from, function ($q) use ($request) {
                return $q->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                return $q->whereDate('created_at', '<=', $request->date_to);
            })
            ->when($request->search, function ($q) use ($request) {
                return $q->where(function ($query) use ($request) {
                    $query->where('name', 'like', "%{$request->search}%")
                        ->orWhere('description', 'like', "%{$request->search}%");
                });
            });

        $documents = $query->latest()->paginate(20);

        return view('documents.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'documentable_type' => 'required|string',
            'documentable_id' => 'required|integer',
            'description' => 'nullable|string|max:1000'
        ]);

        $model = $request->documentable_type::findOrFail($request->documentable_id);
        
        $document = $model->attachDocument(
            $request->file('file'),
            $request->description
        );

        return response()->json([
            'message' => 'Document uploaded successfully',
            'document' => $document
        ]);
    }

    public function destroy(Document $document)
    {
        $document->documentable->detachDocument($document);

        return response()->json([
            'message' => 'Document deleted successfully'
        ]);
    }

    public function download(Document $document)
    {
        if (!Storage::exists($document->file_path)) {
            abort(404);
        }

        return Storage::download(
            $document->file_path,
            $document->name
        );
    }
} 