<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::where('type', 'sales')
            ->withCount('products')
            ->with('children');

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search by name if provided
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->get();

        return view('sales.categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = Category::where('type', 'sales')
            ->whereNull('parent_id')
            ->get();
        return view('sales.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean'
        ]);

        $validated['type'] = 'sales';
        $validated['is_active'] = $request->has('is_active');

        Category::create($validated);

        return redirect()->route('sales.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function show($id)
    {
        return view('sales.categories.show');
    }

    public function edit(Category $category)
    {
        if ($category->type !== 'sales') {
            abort(404);
        }

        $parentCategories = Category::where('type', 'sales')
            ->whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->get();

        return view('sales.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        if ($category->type !== 'sales') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        return redirect()->route('sales.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->type !== 'sales') {
            abort(404);
        }

        // Check if category has products
        if ($category->products()->exists()) {
            return back()->with('error', 'Cannot delete category with associated products.');
        }

        // Check if category has subcategories
        if ($category->children()->exists()) {
            return back()->with('error', 'Cannot delete category with subcategories.');
        }

        $category->delete();

        return redirect()->route('sales.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
} 