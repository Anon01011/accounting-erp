<?php

namespace App\Domains\Accounting\Exports;

use App\Models\JournalEntry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JournalEntryExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $entries;

    public function __construct($entries)
    {
        $this->entries = $entries;
    }

    public function collection()
    {
        return $this->entries;
    }

    public function headings(): array
    {
        return [
            'Reference No',
            'Entry Date',
            'Status',
            'Description',
            'Account',
            'Debit',
            'Credit',
            'Line Description',
            'Created By',
            'Created At',
            'Posted By',
            'Posted At'
        ];
    }

    public function map($entry): array
    {
        $rows = [];
        
        foreach ($entry->items as $item) {
            $rows[] = [
                $entry->reference_no,
                $entry->entry_date->format('Y-m-d'),
                ucfirst($entry->status),
                $entry->description,
                $item->chartOfAccount?->name ?? 'N/A',
                $item->debit ? number_format($item->debit, 2) : '-',
                $item->credit ? number_format($item->credit, 2) : '-',
                $item->description ?? 'N/A',
                $entry->creator?->name ?? 'N/A',
                $entry->created_at->format('Y-m-d H:i:s'),
                $entry->poster?->name ?? 'N/A',
                $entry->posted_at?->format('Y-m-d H:i:s') ?? 'N/A'
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:L' => ['alignment' => ['horizontal' => 'left']],
            'F:G' => ['alignment' => ['horizontal' => 'right']],
        ];
    }
} 