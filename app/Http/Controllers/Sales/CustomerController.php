<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = \App\Models\Customer::latest()->paginate(10);
        return view('sales.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('sales.customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:individual,company',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
        ]);

        $customer = new \App\Models\Customer($validated);
        $customer->save();

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function show($id)
    {
        return view('sales.customers.show');
    }

    public function edit($id)
    {
        return view('sales.customers.edit');
    }

    public function update(Request $request, $id)
    {
        // TODO: Implement customer update logic
    }

    public function destroy($id)
    {
        // TODO: Implement customer deletion logic
    }
} 