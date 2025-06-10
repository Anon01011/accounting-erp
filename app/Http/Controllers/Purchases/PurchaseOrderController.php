<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::latest()->paginate(10);
        return view('purchases.orders.index', compact('purchaseOrders'));
    }

    public function create()
    {
        return view('purchases.orders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'required|date|after:order_date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $order = PurchaseOrder::create([
            'supplier_id' => $validated['supplier_id'],
            'order_date' => $validated['order_date'],
            'expected_delivery_date' => $validated['expected_delivery_date'],
            'status' => 'draft',
            'total_amount' => 0,
        ]);

        foreach ($validated['items'] as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        $order->update([
            'total_amount' => $order->items->sum('total')
        ]);

        return redirect()->route('purchases.orders.index')
            ->with('success', 'Purchase order created successfully.');
    }

    public function show(PurchaseOrder $order)
    {
        return view('purchases.orders.show', compact('order'));
    }

    public function edit(PurchaseOrder $order)
    {
        return view('purchases.orders.edit', compact('order'));
    }

    public function update(Request $request, PurchaseOrder $order)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'required|date|after:order_date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $order->update([
            'supplier_id' => $validated['supplier_id'],
            'order_date' => $validated['order_date'],
            'expected_delivery_date' => $validated['expected_delivery_date'],
        ]);

        $order->items()->delete();

        foreach ($validated['items'] as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        $order->update([
            'total_amount' => $order->items->sum('total')
        ]);

        return redirect()->route('purchases.orders.index')
            ->with('success', 'Purchase order updated successfully.');
    }

    public function destroy(PurchaseOrder $order)
    {
        if ($order->status !== 'draft') {
            return redirect()->route('purchases.orders.index')
                ->with('error', 'Only draft orders can be deleted.');
        }

        $order->items()->delete();
        $order->delete();

        return redirect()->route('purchases.orders.index')
            ->with('success', 'Purchase order deleted successfully.');
    }
} 