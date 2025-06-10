<!DOCTYPE html>
<html>
<head>
    <title>Quotation {{ $quotation->quotation_no }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .content {
            margin-bottom: 30px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Quotation {{ $quotation->quotation_no }}</h1>
        </div>

        <div class="content">
            <p>Dear {{ $quotation->customer->name }},</p>

            <p>{!! nl2br(e($message)) !!}</p>

            <p>Please find attached the quotation for your reference.</p>

            <p>Quotation Details:</p>
            <ul>
                <li>Quotation Number: {{ $quotation->quotation_no }}</li>
                <li>Date: {{ $quotation->quotation_date }}</li>
                <li>Valid Until: {{ $quotation->valid_until }}</li>
                <li>Total Amount: {{ number_format($quotation->total_amount, 2) }}</li>
            </ul>

            <p>If you have any questions, please don't hesitate to contact us.</p>

            <p>Best regards,<br>
            {{ config('app.name') }}</p>
        </div>

        <div class="footer">
            <p>This is an automated message, please do not reply directly to this email.</p>
        </div>
    </div>
</body>
</html> 