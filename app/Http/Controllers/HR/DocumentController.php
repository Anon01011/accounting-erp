<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::with(['employee'])
            ->latest()
            ->paginate(10);
        return view('hr.documents.index', compact('documents'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->get();
        return view('hr.documents.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:contract,id_proof,certificate,other',
            'file' => 'required|file|max:10240', // 10MB max
            'expiry_date' => 'nullable|date|after:today',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('employee-documents', 'public');
            $validated['file_path'] = $path;
            $validated['file_name'] = $request->file('file')->getClientOriginalName();
            $validated['file_type'] = $request->file('file')->getClientMimeType();
            $validated['file_size'] = $request->file('file')->getSize();
        }

        $document = Document::create($validated);

        return redirect()->route('hr.documents.index')
            ->with('success', 'Document uploaded successfully.');
    }

    public function show(Document $document)
    {
        $document->load('employee');
        return view('hr.documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        $employees = Employee::where('is_active', true)->get();
        return view('hr.documents.edit', compact('document', 'employees'));
    }

    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:contract,id_proof,certificate,other',
            'file' => 'nullable|file|max:10240', // 10MB max
            'expiry_date' => 'nullable|date|after:today',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }

            $path = $request->file('file')->store('employee-documents', 'public');
            $validated['file_path'] = $path;
            $validated['file_name'] = $request->file('file')->getClientOriginalName();
            $validated['file_type'] = $request->file('file')->getClientMimeType();
            $validated['file_size'] = $request->file('file')->getSize();
        }

        $document->update($validated);

        return redirect()->route('hr.documents.index')
            ->with('success', 'Document updated successfully.');
    }

    public function destroy(Document $document)
    {
        // Delete file if exists
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('hr.documents.index')
            ->with('success', 'Document deleted successfully.');
    }

    public function download(Document $document)
    {
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return redirect()->route('hr.documents.index')
                ->with('error', 'File not found.');
        }

        return Storage::disk('public')->download(
            $document->file_path,
            $document->file_name
        );
    }

    public function employeeDocuments(Employee $employee)
    {
        $documents = $employee->documents()
            ->latest()
            ->paginate(10);
        return view('hr.documents.employee', compact('employee', 'documents'));
    }

    public function expiringDocuments()
    {
        $documents = Document::with(['employee'])
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->where('expiry_date', '>=', now())
            ->latest('expiry_date')
            ->paginate(10);

        return view('hr.documents.expiring', compact('documents'));
    }

    public function search(Request $request)
    {
        $query = Document::with(['employee']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->filled('expiry_date')) {
            $query->whereDate('expiry_date', $request->expiry_date);
        }

        $documents = $query->latest()->paginate(10);
        $employees = Employee::where('is_active', true)->get();

        return view('hr.documents.search', compact('documents', 'employees'));
    }
} 