<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation {{ $quotation->reference_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .company-info {
            margin-bottom: 20px;
        }
        .quotation-info {
            margin-bottom: 20px;
        }
        .customer-info {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .totals {
            width: 300px;
            margin-left: auto;
        }
        .totals table {
            margin-bottom: 0;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Quotation</h1>
        <h2>{{ $quotation->reference_number }}</h2>
    </div>

    <div class="company-info">
        <h3>{{ config('app.name') }}</h3>
        <p>123 Business Street</p>
        <p>City, State, ZIP</p>
        <p>Phone: (123) 456-7890</p>
        <p>Email: info@company.com</p>
    </div>

    <div class="quotation-info">
        <p><strong>Date:</strong> {{ $quotation->quotation_date->format('Y-m-d') }}</p>
        <p><strong>Valid Until:</strong> {{ $quotation->valid_until->format('Y-m-d') }}</p>
    </div>

    <div class="customer-info">
        <h3>Bill To:</h3>
        <p><strong>{{ $quotation->customer->name }}</strong></p>
        <p>{{ $quotation->customer->address }}</p>
        <p>Phone: {{ $quotation->customer->phone }}</p>
        <p>Email: {{ $quotation->customer->email }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Tax</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->unit_price, 2) }}</td>
                <td>{{ number_format($item->tax_amount, 2) }}</td>
                <td>{{ number_format($item->total_amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td><strong>Subtotal:</strong></td>
                <td>{{ number_format($quotation->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Tax:</strong></td>
                <td>{{ number_format($quotation->tax_amount, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total:</strong></td>
                <td><strong>{{ number_format($quotation->total_amount, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    @if($quotation->notes)
    <div class="notes">
        <h3>Notes:</h3>
        <p>{{ $quotation->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
        <p>Thank you for your business!</p>
    </div>
</body>
</html> 