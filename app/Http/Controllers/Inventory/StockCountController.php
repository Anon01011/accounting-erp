<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockCount;
use App\Models\Warehouse;
use App\Models\Product;

class StockCountController extends Controller
{
    public function index()
    {
        $counts = StockCount::with(['warehouse'])
            ->latest()
            ->paginate(10);
        return view('inventory.counts.index', compact('counts'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        return view('inventory.counts.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'count_date' => 'required|date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.system_quantity' => 'required|numeric|min:0',
            'items.*.counted_quantity' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $count = StockCount::create([
            'warehouse_id' => $validated['warehouse_id'],
            'count_date' => $validated['count_date'],
            'status' => 'draft',
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            $count->items()->create([
                'product_id' => $item['product_id'],
                'system_quantity' => $item['system_quantity'],
                'counted_quantity' => $item['counted_quantity'],
                'notes' => $item['notes'] ?? null,
            ]);
        }

        return redirect()->route('inventory.counts.index')
            ->with('success', 'Stock count created successfully.');
    }

    public function show(StockCount $count)
    {
        $count->load(['warehouse', 'items.product']);
        return view('inventory.counts.show', compact('count'));
    }

    public function edit(StockCount $count)
    {
        if ($count->status !== 'draft') {
            return redirect()->route('inventory.counts.index')
                ->with('error', 'Only draft counts can be edited.');
        }

        $warehouses = Warehouse::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        return view('inventory.counts.edit', compact('count', 'warehouses', 'products'));
    }

    public function update(Request $request, StockCount $count)
    {
        if ($count->status !== 'draft') {
            return redirect()->route('inventory.counts.index')
                ->with('error', 'Only draft counts can be updated.');
        }

        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'count_date' => 'required|date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.system_quantity' => 'required|numeric|min:0',
            'items.*.counted_quantity' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $count->update([
            'warehouse_id' => $validated['warehouse_id'],
            'count_date' => $validated['count_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $count->items()->delete();

        foreach ($validated['items'] as $item) {
            $count->items()->create([
                'product_id' => $item['product_id'],
                'system_quantity' => $item['system_quantity'],
                'counted_quantity' => $item['counted_quantity'],
                'notes' => $item['notes'] ?? null,
            ]);
        }

        return redirect()->route('inventory.counts.index')
            ->with('success', 'Stock count updated successfully.');
    }

    public function destroy(StockCount $count)
    {
        if ($count->status !== 'draft') {
            return redirect()->route('inventory.counts.index')
                ->with('error', 'Only draft counts can be deleted.');
        }

        $count->items()->delete();
        $count->delete();

        return redirect()->route('inventory.counts.index')
            ->with('success', 'Stock count deleted successfully.');
    }

    public function complete(StockCount $count)
    {
        if ($count->status !== 'draft') {
            return redirect()->route('inventory.counts.index')
                ->with('error', 'Only draft counts can be completed.');
        }

        // Update stock levels based on counted quantities
        foreach ($count->items as $item) {
            $stockLevel = $item->product->stockLevels()
                ->where('warehouse_id', $count->warehouse_id)
                ->first();

            if ($stockLevel) {
                $stockLevel->update(['quantity' => $item->counted_quantity]);
            } else {
                $item->product->stockLevels()->create([
                    'warehouse_id' => $count->warehouse_id,
                    'quantity' => $item->counted_quantity,
                ]);
            }
        }

        $count->update(['status' => 'completed']);

        return redirect()->route('inventory.counts.index')
            ->with('success', 'Stock count completed successfully.');
    }
} 