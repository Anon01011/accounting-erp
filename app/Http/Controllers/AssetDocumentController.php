<?php

namespace App\Http\Controllers;

use App\Models\AssetDocument;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssetDocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, ChartOfAccount $asset)
    {
        $request->validate([
            'document' => 'required|file|max:10240', // 10MB max
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'description' => 'nullable|string'
        ]);

        $file = $request->file('document');
        $path = $file->store('asset-documents');

        $document = $asset->documents()->create([
            'name' => $request->name,
            'type' => $request->type,
            'file_path' => $path,
            'description' => $request->description,
            'created_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Document uploaded successfully.');
    }

    public function download(AssetDocument $document)
    {
        if (!Storage::exists($document->file_path)) {
            return redirect()->back()->with('error', 'Document not found.');
        }

        return Storage::download($document->file_path, $document->name);
    }

    public function destroy(AssetDocument $document)
    {
        if (Storage::exists($document->file_path)) {
            Storage::delete($document->file_path);
        }

        $document->delete();

        return redirect()->back()->with('success', 'Document deleted successfully.');
    }
}
