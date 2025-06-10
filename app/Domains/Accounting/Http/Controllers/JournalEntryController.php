<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Services\JournalEntryExportService;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    public function exportPdf()
    {
        $entries = JournalEntry::with(['items.chartOfAccount', 'creator', 'poster'])
            ->orderBy('entry_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $exportService = app(JournalEntryExportService::class);
        return $exportService->exportToPdf($entries);
    }

    public function exportExcel()
    {
        $entries = JournalEntry::with(['items.chartOfAccount', 'creator', 'poster'])
            ->orderBy('entry_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $exportService = app(JournalEntryExportService::class);
        return $exportService->exportToExcel($entries);
    }
} 