<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use PDF;
use Excel;
use App\Domains\Accounting\Services\JournalEntryExportService;

class JournalEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $entries = JournalEntry::with(['creator', 'items.chartOfAccount'])
                ->latest()
                ->paginate(10);

            return view('journal-entries.index', compact('entries'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading journal entries: ' . $e->getMessage());
        }
    }

    /**
     * Generate a unique reference number for journal entry
     */
    private function generateReferenceNumber(): string
    {
        $prefix = 'JE';
        $date = now()->format('Ymd');
        $time = now()->format('Hi');
        
        // Get the last reference number for today
        $lastEntry = JournalEntry::where('reference_no', 'like', "{$prefix}-{$date}-%")
            ->orderBy('reference_no', 'desc')
            ->first();

        if ($lastEntry) {
            // Extract the sequence number and increment
            $sequence = (int) substr($lastEntry->reference_no, -4);
            $sequence++;
        } else {
            $sequence = 1;
        }

        return sprintf('%s-%s-%s%04d', $prefix, $date, $time, $sequence);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $accounts = ChartOfAccount::where('is_active', true)
                ->orderBy('type_code')
                ->orderBy('group_code')
                ->orderBy('class_code')
                ->orderBy('account_code')
                ->get();

            $referenceNo = $this->generateReferenceNumber();

            return view('journal-entries.create', compact('accounts', 'referenceNo'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading create form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'reference_no' => ['required', 'string', 'unique:journal_entries'],
                'entry_date' => 'required|date',
                'description' => 'required|string',
                'items' => 'required|array|min:2',
                'items.*.chart_of_account_id' => 'required|exists:chart_of_accounts,id',
                'items.*.debit' => 'required|numeric|min:0',
                'items.*.credit' => 'required|numeric|min:0',
                'items.*.description' => 'nullable|string'
            ], [
                'items.required' => 'At least one journal entry line is required.',
                'items.min' => 'At least two journal entry lines are required.',
                'items.*.chart_of_account_id.required' => 'Account is required for each line.',
                'items.*.chart_of_account_id.exists' => 'Selected account is invalid.',
                'items.*.debit.required' => 'Debit amount is required.',
                'items.*.debit.numeric' => 'Debit amount must be a number.',
                'items.*.debit.min' => 'Debit amount cannot be negative.',
                'items.*.credit.required' => 'Credit amount is required.',
                'items.*.credit.numeric' => 'Credit amount must be a number.',
                'items.*.credit.min' => 'Credit amount cannot be negative.'
            ]);

            // Validate that total debits equal total credits
            $totalDebit = collect($request->items)->sum('debit');
            $totalCredit = collect($request->items)->sum('credit');

            if (abs($totalDebit - $totalCredit) > 0.01) {
                if ($request->ajax()) {
                    return response()->json([
                        'message' => 'Total debits must equal total credits.',
                        'errors' => ['items' => ['Total debits must equal total credits.']]
                    ], 422);
                }
                throw new \Exception('Total debits must equal total credits.');
            }

            DB::beginTransaction();

            $entry = JournalEntry::create([
                'reference_no' => $validated['reference_no'],
                'entry_date' => $validated['entry_date'],
                'description' => $validated['description'],
                'status' => 'draft',
                'created_by' => Auth::id()
            ]);

            foreach ($validated['items'] as $item) {
                $entry->items()->create([
                    'chart_of_account_id' => $item['chart_of_account_id'],
                    'debit' => $item['debit'],
                    'credit' => $item['credit'],
                    'description' => $item['description'] ?? null,
                    'created_by' => Auth::id()
                ]);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Journal entry created successfully.',
                    'redirect' => route('journal-entries.show', $entry)
                ]);
            }

            return redirect()
                ->route('journal-entries.show', $entry)
                ->with('success', 'Journal entry created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Error creating journal entry: ' . $e->getMessage()
                ], 500);
            }
            return redirect()
                ->back()
                ->with('error', 'Error creating journal entry: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(JournalEntry $journal_entry)
    {
        try {
            $journal_entry->load(['creator', 'updater', 'items.chartOfAccount']);
            return view('journal-entries.show', compact('journal_entry'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading journal entry details: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JournalEntry $journalEntry)
    {
        try {
            if ($journalEntry->status !== 'draft') {
                throw new \Exception('Only draft entries can be edited.');
            }

            $journalEntry->load('items');
            $accounts = ChartOfAccount::where('is_active', true)
                ->orderBy('type_code')
                ->orderBy('group_code')
                ->orderBy('class_code')
                ->orderBy('account_code')
                ->get();

            return view('journal-entries.edit', compact('journalEntry', 'accounts'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading edit form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JournalEntry $journalEntry)
    {
        try {
            if ($journalEntry->status !== 'draft') {
                throw new \Exception('Only draft entries can be edited.');
            }

            $validated = $request->validate([
                'reference_no' => ['required', 'string', Rule::unique('journal_entries')->ignore($journalEntry->id)],
                'entry_date' => 'required|date',
                'description' => 'required|string',
                'items' => 'required|array|min:2',
                'items.*.chart_of_account_id' => 'required|exists:chart_of_accounts,id',
                'items.*.debit' => 'required|numeric|min:0',
                'items.*.credit' => 'required|numeric|min:0',
                'items.*.description' => 'nullable|string'
            ]);

            // Validate that total debits equal total credits
            $totalDebit = collect($request->items)->sum('debit');
            $totalCredit = collect($request->items)->sum('credit');

            if (abs($totalDebit - $totalCredit) > 0.01) {
                throw new \Exception('Total debits must equal total credits.');
            }

            DB::beginTransaction();

            $journalEntry->update([
                'reference_no' => $validated['reference_no'],
                'entry_date' => $validated['entry_date'],
                'description' => $validated['description'],
                'updated_by' => Auth::id()
            ]);

            // Delete existing items
            $journalEntry->items()->delete();

            // Create new items
            foreach ($validated['items'] as $item) {
                $journalEntry->items()->create([
                    'chart_of_account_id' => $item['chart_of_account_id'],
                    'debit' => $item['debit'],
                    'credit' => $item['credit'],
                    'description' => $item['description'] ?? null
                ]);
            }

            DB::commit();

            return redirect()
                ->route('journal-entries.show', $journalEntry)
                ->with('success', 'Journal entry updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Error updating journal entry: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JournalEntry $journalEntry)
    {
        try {
            if ($journalEntry->status !== 'draft') {
                throw new \Exception('Only draft entries can be deleted.');
            }

            DB::beginTransaction();
            
            $journalEntry->items()->delete();
            $journalEntry->delete();
            
            DB::commit();

            return redirect()
                ->route('journal-entries.index')
                ->with('success', 'Journal entry deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Error deleting journal entry: ' . $e->getMessage());
        }
    }

    public function post(JournalEntry $journalEntry)
    {
        try {
            if ($journalEntry->status !== 'draft') {
                throw new \Exception('Only draft entries can be posted.');
            }

            DB::beginTransaction();
            
            $journalEntry->update([
                'status' => 'posted',
                'posted_by' => Auth::id(),
                'posted_at' => now(),
            ]);
            
            DB::commit();

            return redirect()
                ->route('journal-entries.show', $journalEntry)
                ->with('success', 'Journal entry posted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Error posting journal entry: ' . $e->getMessage());
        }
    }

    public function void(JournalEntry $journalEntry)
    {
        try {
            if ($journalEntry->status !== 'posted') {
                throw new \Exception('Only posted entries can be voided.');
            }

            DB::beginTransaction();
            
            $journalEntry->update([
                'status' => 'void',
                'updated_by' => Auth::id()
            ]);
            
            DB::commit();

            return redirect()
                ->route('journal-entries.show', $journalEntry)
                ->with('success', 'Journal entry voided successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Error voiding journal entry: ' . $e->getMessage());
        }
    }

    /**
     * Export journal entries to PDF
     */
    public function exportPdf()
    {
        try {
            $entries = JournalEntry::with(['items.chartOfAccount', 'creator', 'poster'])
                ->orderBy('entry_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            $pdf = PDF::loadView('journal-entries.export.pdf', [
                'entries' => $entries,
                'date' => now()->format('Y-m-d H:i:s')
            ]);

            return $pdf->download('journal-entries-' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error exporting to PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export journal entries to Excel
     */
    public function exportExcel()
    {
        try {
            $service = new JournalEntryExportService();
            $entries = JournalEntry::with(['creator', 'poster', 'items.chartOfAccount'])
                                    ->latest()
                                    ->get();
            return $service->exportToExcel($entries);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error exporting to Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export a single specified journal entry to PDF.
     */
    public function exportSinglePdf(JournalEntry $journalEntry)
    {
        try {
            $service = new JournalEntryExportService();
            $entries = collect([$journalEntry->load(['creator', 'poster', 'items.chartOfAccount'])]);
            return $service->exportToPdf($entries);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error exporting single entry to PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export a single specified journal entry to Excel.
     */
    public function exportSingleExcel(JournalEntry $journalEntry)
    {
        try {
            $service = new JournalEntryExportService();
            $entries = collect([$journalEntry->load(['creator', 'poster', 'items.chartOfAccount'])]);
            return $service->exportToExcel($entries);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error exporting single entry to Excel: ' . $e->getMessage());
        }
    }
}
