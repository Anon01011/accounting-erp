<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Models\Product;

class StockTransferController extends Controller
{
    public function index()
    {
        $transfers = StockTransfer::with(['sourceWarehouse', 'destinationWarehouse'])
            ->latest()
            ->paginate(10);
        return view('inventory.transfers.index', compact('transfers'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        return view('inventory.transfers.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'source_warehouse_id' => 'required|exists:warehouses,id',
            'destination_warehouse_id' => 'required|exists:warehouses,id|different:source_warehouse_id',
            'transfer_date' => 'required|date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'notes' => 'nullable|string',
        ]);

        $transfer = StockTransfer::create([
            'source_warehouse_id' => $validated['source_warehouse_id'],
            'destination_warehouse_id' => $validated['destination_warehouse_id'],
            'transfer_date' => $validated['transfer_date'],
            'status' => 'draft',
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            $transfer->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        return redirect()->route('inventory.transfers.index')
            ->with('success', 'Stock transfer created successfully.');
    }

    public function show(StockTransfer $transfer)
    {
        $transfer->load(['sourceWarehouse', 'destinationWarehouse', 'items.product']);
        return view('inventory.transfers.show', compact('transfer'));
    }

    public function edit(StockTransfer $transfer)
    {
        if ($transfer->status !== 'draft') {
            return redirect()->route('inventory.transfers.index')
                ->with('error', 'Only draft transfers can be edited.');
        }

        $warehouses = Warehouse::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        return view('inventory.transfers.edit', compact('transfer', 'warehouses', 'products'));
    }

    public function update(Request $request, StockTransfer $transfer)
    {
        if ($transfer->status !== 'draft') {
            return redirect()->route('inventory.transfers.index')
                ->with('error', 'Only draft transfers can be updated.');
        }

        $validated = $request->validate([
            'source_warehouse_id' => 'required|exists:warehouses,id',
            'destination_warehouse_id' => 'required|exists:warehouses,id|different:source_warehouse_id',
            'transfer_date' => 'required|date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'notes' => 'nullable|string',
        ]);

        $transfer->update([
            'source_warehouse_id' => $validated['source_warehouse_id'],
            'destination_warehouse_id' => $validated['destination_warehouse_id'],
            'transfer_date' => $validated['transfer_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $transfer->items()->delete();

        foreach ($validated['items'] as $item) {
            $transfer->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        return redirect()->route('inventory.transfers.index')
            ->with('success', 'Stock transfer updated successfully.');
    }

    public function destroy(StockTransfer $transfer)
    {
        if ($transfer->status !== 'draft') {
            return redirect()->route('inventory.transfers.index')
                ->with('error', 'Only draft transfers can be deleted.');
        }

        $transfer->items()->delete();
        $transfer->delete();

        return redirect()->route('inventory.transfers.index')
            ->with('success', 'Stock transfer deleted successfully.');
    }

    public function complete(StockTransfer $transfer)
    {
        if ($transfer->status !== 'draft') {
            return redirect()->route('inventory.transfers.index')
                ->with('error', 'Only draft transfers can be completed.');
        }

        // Update stock levels in both warehouses
        foreach ($transfer->items as $item) {
            // Decrease stock in source warehouse
            $sourceStock = $item->product->stockLevels()
                ->where('warehouse_id', $transfer->source_warehouse_id)
                ->first();
            
            if (!$sourceStock || $sourceStock->quantity < $item->quantity) {
                return redirect()->route('inventory.transfers.index')
                    ->with('error', 'Insufficient stock in source warehouse.');
            }

            $sourceStock->decrement('quantity', $item->quantity);

            // Increase stock in destination warehouse
            $destinationStock = $item->product->stockLevels()
                ->where('warehouse_id', $transfer->destination_warehouse_id)
                ->first();

            if ($destinationStock) {
                $destinationStock->increment('quantity', $item->quantity);
            } else {
                $item->product->stockLevels()->create([
                    'warehouse_id' => $transfer->destination_warehouse_id,
                    'quantity' => $item->quantity,
                ]);
            }
        }

        $transfer->update(['status' => 'completed']);

        return redirect()->route('inventory.transfers.index')
            ->with('success', 'Stock transfer completed successfully.');
    }
} 