<?php

namespace App\Http\Controllers;

use App\Models\ItemMaster;
use Illuminate\Http\Request;

class ItemMasterController extends Controller
{
    public function index()
    {
        $items = ItemMaster::with('category')->paginate(15);
        return view('item-masters.index', compact('items'));
    }

    public function create()
    {
        return view('item-masters.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:item_categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'is_active' => 'boolean',
        ]);

        ItemMaster::create($request->all());

        return redirect()->route('item-masters.index')->with('success', 'Item created successfully.');
    }

    public function show(ItemMaster $itemMaster)
    {
        return view('item-masters.show', compact('itemMaster'));
    }

    public function edit(ItemMaster $itemMaster)
    {
        return view('item-masters.edit', compact('itemMaster'));
    }

    public function update(Request $request, ItemMaster $itemMaster)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:item_categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'is_active' => 'boolean',
        ]);

        $itemMaster->update($request->all());

        return redirect()->route('item-masters.index')->with('success', 'Item updated successfully.');
    }

    public function destroy(ItemMaster $itemMaster)
    {
        $itemMaster->delete();

        return redirect()->route('item-masters.index')->with('success', 'Item deleted successfully.');
    }
}
