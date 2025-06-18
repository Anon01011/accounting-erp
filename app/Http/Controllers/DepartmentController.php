<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::paginate(15);
        return view('departments.index', compact('departments'));
    }

    // Additional methods (create, store, edit, update, destroy) can be added here as needed

    public function create()
    {
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code',
            'status' => 'nullable|boolean',
        ]);

        $data = $request->only(['name', 'code', 'status']);
        $data['status'] = $request->has('status') ? (bool)$request->status : false;

        \App\Models\Department::create($data);

        return redirect()->route('departments.index')->with('success', 'Department created successfully.');
    }

    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code,' . $department->id,
            'status' => 'nullable|boolean',
        ]);

        $data = $request->only(['name', 'code', 'status']);
        $data['status'] = $request->has('status') ? (bool)$request->status : false;

        $department->update($data);

        return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
    }
}
