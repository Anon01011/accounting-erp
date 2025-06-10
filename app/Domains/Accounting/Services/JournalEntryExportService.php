<?php

namespace App\Domains\Accounting\Services;

use App\Models\JournalEntry;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use PDF;
use Excel;
use Illuminate\Support\Facades\DB;

class JournalEntryExportService
{
    public function exportToPdf(Collection $entries)
    {
        $pdf = PDF::loadView('journal-entries.export.pdf', [
            'entries' => $entries,
            'date' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        return $pdf->download('journal-entries-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    public function exportToExcel(Collection $entries)
    {
        return Excel::download(new JournalEntryExport($entries), 'journal-entries-' . Carbon::now()->format('Y-m-d') . '.xlsx');
    }

    public function batchPost(Collection $entries)
    {
        $errors = [];
        $success = [];

        DB::beginTransaction();
        try {
            foreach ($entries as $entry) {
                if ($entry->status === 'draft') {
                    $validationService = app(JournalEntryValidationService::class);
                    $validationErrors = $validationService->validate($entry);

                    if (empty($validationErrors)) {
                        $entry->status = 'posted';
                        $entry->posted_at = now();
                        $entry->posted_by = auth()->id();
                        $entry->save();
                        $success[] = $entry->reference_no;
                    } else {
                        $errors[] = [
                            'reference_no' => $entry->reference_no,
                            'errors' => $validationErrors
                        ];
                    }
                }
            }

            if (empty($errors)) {
                DB::commit();
                return [
                    'success' => true,
                    'message' => 'Successfully posted ' . count($success) . ' entries.',
                    'data' => $success
                ];
            } else {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Failed to post some entries.',
                    'errors' => $errors
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'An error occurred during batch posting.',
                'error' => $e->getMessage()
            ];
        }
    }

    public function search(array $filters)
    {
        $query = JournalEntry::query()
            ->with(['items.chartOfAccount', 'creator', 'poster']);

        if (!empty($filters['date_from'])) {
            $query->whereDate('entry_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('entry_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['reference_no'])) {
            $query->where('reference_no', 'like', '%' . $filters['reference_no'] . '%');
        }

        if (!empty($filters['account_id'])) {
            $query->whereHas('items', function ($q) use ($filters) {
                $q->where('chart_of_account_id', $filters['account_id']);
            });
        }

        if (!empty($filters['amount_from'])) {
            $query->whereHas('items', function ($q) use ($filters) {
                $q->where(function ($q) use ($filters) {
                    $q->where('debit', '>=', $filters['amount_from'])
                      ->orWhere('credit', '>=', $filters['amount_from']);
                });
            });
        }

        if (!empty($filters['amount_to'])) {
            $query->whereHas('items', function ($q) use ($filters) {
                $q->where(function ($q) use ($filters) {
                    $q->where('debit', '<=', $filters['amount_to'])
                      ->orWhere('credit', '<=', $filters['amount_to']);
                });
            });
        }

        return $query->orderBy('entry_date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
    }
} 