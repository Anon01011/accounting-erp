<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseReturn;

class PurchaseReturnController extends Controller
{
    public function index()
    {
        $returns = PurchaseReturn::latest()->paginate(10);
        return view('purchases.returns.index', compact('returns'));
    }

    public function create()
    {
        return view('purchases.returns.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_receipt_id' => 'required|exists:purchase_receipts,id',
            'return_date' => 'required|date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $return = PurchaseReturn::create([
            'purchase_receipt_id' => $validated['purchase_receipt_id'],
            'return_date' => $validated['return_date'],
            'status' => 'draft',
            'total_amount' => 0,
            'reason' => $validated['reason'],
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            $return->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        $return->update([
            'total_amount' => $return->items->sum('total')
        ]);

        return redirect()->route('purchases.returns.index')
            ->with('success', 'Purchase return created successfully.');
    }

    public function show(PurchaseReturn $return)
    {
        return view('purchases.returns.show', compact('return'));
    }

    public function edit(PurchaseReturn $return)
    {
        return view('purchases.returns.edit', compact('return'));
    }

    public function update(Request $request, PurchaseReturn $return)
    {
        $validated = $request->validate([
            'purchase_receipt_id' => 'required|exists:purchase_receipts,id',
            'return_date' => 'required|date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $return->update([
            'purchase_receipt_id' => $validated['purchase_receipt_id'],
            'return_date' => $validated['return_date'],
            'reason' => $validated['reason'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $return->items()->delete();

        foreach ($validated['items'] as $item) {
            $return->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        $return->update([
            'total_amount' => $return->items->sum('total')
        ]);

        return redirect()->route('purchases.returns.index')
            ->with('success', 'Purchase return updated successfully.');
    }

    public function destroy(PurchaseReturn $return)
    {
        if ($return->status !== 'draft') {
            return redirect()->route('purchases.returns.index')
                ->with('error', 'Only draft returns can be deleted.');
        }

        $return->items()->delete();
        $return->delete();

        return redirect()->route('purchases.returns.index')
            ->with('success', 'Purchase return deleted successfully.');
    }
} 