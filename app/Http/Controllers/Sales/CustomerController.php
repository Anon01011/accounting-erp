<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::paginate(15);
        return view('sales.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('sales.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'tax_number' => 'nullable|string|max:100',
            'credit_limit' => 'nullable|numeric',
            'status' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        Customer::create($request->all());

        return redirect()->route('sales.customers.index')->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        return view('sales.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('sales.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'tax_number' => 'nullable|string|max:100',
            'credit_limit' => 'nullable|numeric',
            'status' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $customer->update($request->all());

        return redirect()->route('sales.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('sales.customers.index')->with('success', 'Customer deleted successfully.');
    }
}
