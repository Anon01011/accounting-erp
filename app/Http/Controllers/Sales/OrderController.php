<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesOrder;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index()
    {
        $orders = SalesOrder::with('customer')->latest()->paginate(10);
        return view('sales.orders.index', compact('orders'));
    }

    public function create()
    {
        $customers = \App\Models\Customer::orderBy('name')->get();
        $products = \App\Models\Product::orderBy('name')->get();
        return view('sales.orders.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'required|date|after:order_date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.description' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $order = SalesOrder::create([
            'reference_number' => 'SO-' . str_pad(SalesOrder::count() + 1, 4, '0', STR_PAD_LEFT),
            'customer_id' => $validated['customer_id'],
            'order_date' => $validated['order_date'],
            'expected_delivery_date' => $validated['expected_delivery_date'],
            'status' => 'draft',
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id()
        ]);

        $totalAmount = 0;
        foreach ($validated['items'] as $item) {
            $itemTotal = $item['quantity'] * $item['unit_price'];
            $totalAmount += $itemTotal;
            
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $itemTotal,
                'description' => $item['description'] ?? null,
                'created_by' => auth()->id()
            ]);
        }

        $order->update(['total_amount' => $totalAmount]);

        return redirect()->route('sales.orders.show', $order)
            ->with('success', 'Sales order created successfully.');
    }

    public function show(SalesOrder $order)
    {
        $order->load(['customer', 'items.product']);
        return view('sales.orders.show', compact('order'));
    }

    public function edit($id)
    {
        $order = \App\Models\SalesOrder::with(['customer', 'items.product'])->findOrFail($id);
        $customers = \App\Models\Customer::orderBy('name')->get();
        $products = \App\Models\Product::orderBy('name')->get();
        return view('sales.orders.edit', compact('order', 'customers', 'products'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'required|date|after:order_date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.description' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $order = SalesOrder::findOrFail($id);
        
        $order->update([
            'customer_id' => $validated['customer_id'],
            'order_date' => $validated['order_date'],
            'expected_delivery_date' => $validated['expected_delivery_date'],
            'notes' => $validated['notes'] ?? null,
            'updated_by' => auth()->id()
        ]);

        // Delete existing items
        $order->items()->delete();

        // Create new items
        $totalAmount = 0;
        foreach ($validated['items'] as $item) {
            $itemTotal = $item['quantity'] * $item['unit_price'];
            $totalAmount += $itemTotal;
            
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $itemTotal,
                'description' => $item['description'] ?? null,
                'created_by' => auth()->id()
            ]);
        }

        $order->update(['total_amount' => $totalAmount]);

        return redirect()->route('sales.orders.show', $order)
            ->with('success', 'Sales order updated successfully.');
    }

    public function destroy($id)
    {
        // TODO: Implement order deletion logic
    }

    public function approve($id)
    {
        // TODO: Implement order approval logic
    }

    public function reject($id)
    {
        // TODO: Implement order rejection logic
    }

    public function pdf(SalesOrder $order)
    {
        $order->load(['customer', 'items.product']);
        $pdf = PDF::loadView('sales.orders.pdf', compact('order'));
        return $pdf->stream('sales-order-' . $order->reference_number . '.pdf');
    }
} 