<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseReceipt;

class PurchaseReceiptController extends Controller
{
    public function index()
    {
        $receipts = PurchaseReceipt::latest()->paginate(10);
        return view('purchases.receipts.index', compact('receipts'));
    }

    public function create()
    {
        return view('purchases.receipts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'receipt_date' => 'required|date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $receipt = PurchaseReceipt::create([
            'purchase_order_id' => $validated['purchase_order_id'],
            'receipt_date' => $validated['receipt_date'],
            'status' => 'draft',
            'total_amount' => 0,
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            $receipt->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        $receipt->update([
            'total_amount' => $receipt->items->sum('total')
        ]);

        return redirect()->route('purchases.receipts.index')
            ->with('success', 'Purchase receipt created successfully.');
    }

    public function show(PurchaseReceipt $receipt)
    {
        return view('purchases.receipts.show', compact('receipt'));
    }

    public function edit(PurchaseReceipt $receipt)
    {
        return view('purchases.receipts.edit', compact('receipt'));
    }

    public function update(Request $request, PurchaseReceipt $receipt)
    {
        $validated = $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'receipt_date' => 'required|date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $receipt->update([
            'purchase_order_id' => $validated['purchase_order_id'],
            'receipt_date' => $validated['receipt_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $receipt->items()->delete();

        foreach ($validated['items'] as $item) {
            $receipt->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        $receipt->update([
            'total_amount' => $receipt->items->sum('total')
        ]);

        return redirect()->route('purchases.receipts.index')
            ->with('success', 'Purchase receipt updated successfully.');
    }

    public function destroy(PurchaseReceipt $receipt)
    {
        if ($receipt->status !== 'draft') {
            return redirect()->route('purchases.receipts.index')
                ->with('error', 'Only draft receipts can be deleted.');
        }

        $receipt->items()->delete();
        $receipt->delete();

        return redirect()->route('purchases.receipts.index')
            ->with('success', 'Purchase receipt deleted successfully.');
    }
} 