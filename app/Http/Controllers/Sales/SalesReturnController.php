<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\SalesReturn;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;

class SalesReturnController extends Controller
{
    public function index()
    {
        $returns = SalesReturn::with(['customer', 'invoice'])->latest()->paginate(10);
        return view('sales.returns.index', compact('returns'));
    }

    public function create()
    {
        $customers = Customer::active()->get();
        $invoices = Invoice::where('status', 'paid')->get();
        return view('sales.returns.create', compact('customers', 'invoices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference_number' => 'required|unique:sales_returns',
            'invoice_id' => 'required|exists:invoices,id',
            'customer_id' => 'required|exists:customers,id',
            'return_date' => 'required|date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $return = SalesReturn::create([
            'reference_number' => $validated['reference_number'],
            'invoice_id' => $validated['invoice_id'],
            'customer_id' => $validated['customer_id'],
            'return_date' => $validated['return_date'],
            'status' => 'pending',
            'notes' => $validated['notes'],
            'created_by' => auth()->id()
        ]);

        foreach ($validated['items'] as $item) {
            $return->items()->create($item);
        }

        return redirect()->route('sales.returns.index')
            ->with('success', 'Sales return created successfully.');
    }

    public function show(SalesReturn $return)
    {
        $return->load(['customer', 'invoice', 'items.product']);
        return view('sales.returns.show', compact('return'));
    }

    public function edit(SalesReturn $return)
    {
        if ($return->status !== 'pending') {
            return redirect()->route('sales.returns.index')
                ->with('error', 'Cannot edit a processed return.');
        }

        $customers = Customer::active()->get();
        $invoices = Invoice::where('status', 'paid')->get();
        return view('sales.returns.edit', compact('return', 'customers', 'invoices'));
    }

    public function update(Request $request, SalesReturn $return)
    {
        if ($return->status !== 'pending') {
            return redirect()->route('sales.returns.index')
                ->with('error', 'Cannot update a processed return.');
        }

        $validated = $request->validate([
            'reference_number' => 'required|unique:sales_returns,reference_number,' . $return->id,
            'invoice_id' => 'required|exists:invoices,id',
            'customer_id' => 'required|exists:customers,id',
            'return_date' => 'required|date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $return->update([
            'reference_number' => $validated['reference_number'],
            'invoice_id' => $validated['invoice_id'],
            'customer_id' => $validated['customer_id'],
            'return_date' => $validated['return_date'],
            'notes' => $validated['notes'],
            'updated_by' => auth()->id()
        ]);

        $return->items()->delete();
        foreach ($validated['items'] as $item) {
            $return->items()->create($item);
        }

        return redirect()->route('sales.returns.index')
            ->with('success', 'Sales return updated successfully.');
    }

    public function destroy(SalesReturn $return)
    {
        if ($return->status !== 'pending') {
            return redirect()->route('sales.returns.index')
                ->with('error', 'Cannot delete a processed return.');
        }

        $return->items()->delete();
        $return->delete();

        return redirect()->route('sales.returns.index')
            ->with('success', 'Sales return deleted successfully.');
    }

    public function approve(SalesReturn $return)
    {
        if ($return->status !== 'pending') {
            return redirect()->route('sales.returns.index')
                ->with('error', 'This return has already been processed.');
        }

        $return->update([
            'status' => 'approved',
            'updated_by' => auth()->id()
        ]);

        // Update inventory
        foreach ($return->items as $item) {
            $product = $item->product;
            $product->increment('quantity', $item->quantity);
        }

        return redirect()->route('sales.returns.index')
            ->with('success', 'Sales return approved successfully.');
    }
} 