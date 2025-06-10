<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function salesSummary()
    {
        return view('sales.reports.sales-summary');
    }

    public function customerStatement()
    {
        return view('sales.reports.customer-statement');
    }

    public function productPerformance()
    {
        return view('sales.reports.product-performance');
    }
} 