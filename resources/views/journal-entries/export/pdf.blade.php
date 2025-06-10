<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Journal Entries Report</title>
    <style>
        @page { size: A4 landscape; }
        body {
            font-family: Arial, sans-serif;
            font-size: 7px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            color: #01657F;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid black;
            padding: 2px;
            text-align: left;
            word-wrap: break-word;
        }
        th {
            background-color: #01657F;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Journal Entries Report</h1>
        <p>Generated on: {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 7%;">Reference No</th>
                <th style="width: 6%;">Entry Date</th>
                <th style="width: 5%;">Status</th>
                <th style="width: 10%;">Description</th>
                <th style="width: 9%;">Account</th>
                <th class="text-right" style="width: 5%;">Debit</th>
                <th class="text-right" style="width: 5%;">Credit</th>
                <th style="width: 10%;">Line Description</th>
                <th style="width: 5%;">Created By</th>
                <th style="width: 6%;">Created At</th>
                <th style="width: 5%;">Posted By</th>
                <th style="width: 6%;">Posted At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $entry)
                @foreach($entry->items as $item)
                    <tr>
                        <td>{{ $entry->reference_no }}</td>
                        <td>{{ $entry->entry_date->format('Y-m-d') }}</td>
                        <td>{{ ucfirst($entry->status) }}</td>
                        <td>{{ $entry->description }}</td>
                        <td>{{ $item->chartOfAccount?->name ?? 'N/A' }}</td>
                        <td class="text-right">{{ $item->debit ? number_format($item->debit, 2) : '-' }}</td>
                        <td class="text-right">{{ $item->credit ? number_format($item->credit, 2) : '-' }}</td>
                        <td>{{ $item->description ?? 'N/A' }}</td>
                        <td>{{ $entry->creator?->name ?? 'N/A' }}</td>
                        <td>{{ $entry->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>{{ $entry->poster?->name ?? 'N/A' }}</td>
                        <td>{{ $entry->posted_at?->format('Y-m-d H:i:s') ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
    </div>
</body>
</html> 