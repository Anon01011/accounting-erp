<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('sales.categories.index');
    }

    public function create()
    {
        return view('sales.categories.create');
    }

    public function store(Request $request)
    {
        // TODO: Implement category creation logic
    }

    public function show($id)
    {
        return view('sales.categories.show');
    }

    public function edit($id)
    {
        return view('sales.categories.edit');
    }

    public function update(Request $request, $id)
    {
        // TODO: Implement category update logic
    }

    public function destroy($id)
    {
        // TODO: Implement category deletion logic
    }
} 