<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        return view('sales.invoices.index');
    }

    public function create()
    {
        return view('sales.invoices.create');
    }

    public function store(Request $request)
    {
        // TODO: Implement invoice creation logic
    }

    public function show($id)
    {
        return view('sales.invoices.show');
    }

    public function edit($id)
    {
        return view('sales.invoices.edit');
    }

    public function update(Request $request, $id)
    {
        // TODO: Implement invoice update logic
    }

    public function destroy($id)
    {
        // TODO: Implement invoice deletion logic
    }

    public function post($id)
    {
        // TODO: Implement invoice posting logic
    }

    public function void($id)
    {
        // TODO: Implement invoice voiding logic
    }
} 