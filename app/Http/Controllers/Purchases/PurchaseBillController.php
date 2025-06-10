<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseBill;

class PurchaseBillController extends Controller
{
    public function index()
    {
        $bills = PurchaseBill::latest()->paginate(10);
        return view('purchases.bills.index', compact('bills'));
    }

    public function create()
    {
        return view('purchases.bills.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_receipt_id' => 'required|exists:purchase_receipts,id',
            'bill_date' => 'required|date',
            'due_date' => 'required|date|after:bill_date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $bill = PurchaseBill::create([
            'purchase_receipt_id' => $validated['purchase_receipt_id'],
            'bill_date' => $validated['bill_date'],
            'due_date' => $validated['due_date'],
            'status' => 'draft',
            'subtotal' => 0,
            'tax_amount' => 0,
            'total_amount' => 0,
            'tax_rate' => $validated['tax_rate'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $total = $item['quantity'] * $item['unit_price'];
            $subtotal += $total;
            
            $bill->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $total,
            ]);
        }

        $taxAmount = $subtotal * ($validated['tax_rate'] / 100);
        $totalAmount = $subtotal + $taxAmount;

        $bill->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
        ]);

        return redirect()->route('purchases.bills.index')
            ->with('success', 'Purchase bill created successfully.');
    }

    public function show(PurchaseBill $bill)
    {
        return view('purchases.bills.show', compact('bill'));
    }

    public function edit(PurchaseBill $bill)
    {
        return view('purchases.bills.edit', compact('bill'));
    }

    public function update(Request $request, PurchaseBill $bill)
    {
        $validated = $request->validate([
            'purchase_receipt_id' => 'required|exists:purchase_receipts,id',
            'bill_date' => 'required|date',
            'due_date' => 'required|date|after:bill_date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $bill->update([
            'purchase_receipt_id' => $validated['purchase_receipt_id'],
            'bill_date' => $validated['bill_date'],
            'due_date' => $validated['due_date'],
            'tax_rate' => $validated['tax_rate'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $bill->items()->delete();

        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $total = $item['quantity'] * $item['unit_price'];
            $subtotal += $total;
            
            $bill->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $total,
            ]);
        }

        $taxAmount = $subtotal * ($validated['tax_rate'] / 100);
        $totalAmount = $subtotal + $taxAmount;

        $bill->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
        ]);

        return redirect()->route('purchases.bills.index')
            ->with('success', 'Purchase bill updated successfully.');
    }

    public function destroy(PurchaseBill $bill)
    {
        if ($bill->status !== 'draft') {
            return redirect()->route('purchases.bills.index')
                ->with('error', 'Only draft bills can be deleted.');
        }

        $bill->items()->delete();
        $bill->delete();

        return redirect()->route('purchases.bills.index')
            ->with('success', 'Purchase bill deleted successfully.');
    }
} 