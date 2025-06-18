<?php

namespace App\Http\Controllers;

use App\Models\GLControlGroup;
use Illuminate\Http\Request;

class GLControlGroupController extends Controller
{
    public function index()
    {
        $glControlGroups = GLControlGroup::paginate(15);
        return view('gl-control-groups.index', compact('glControlGroups'));
    }

    public function create()
    {
        return view('gl-control-groups.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:gl_control_groups,code',
            'description' => 'nullable|string',
        ]);

        GLControlGroup::create($request->all());

        return redirect()->route('gl-control-groups.index')->with('success', 'GL Control Group created successfully.');
    }

    public function show(GLControlGroup $glControlGroup)
    {
        return view('gl-control-groups.show', compact('glControlGroup'));
    }

    public function edit(GLControlGroup $glControlGroup)
    {
        return view('gl-control-groups.edit', compact('glControlGroup'));
    }

    public function update(Request $request, GLControlGroup $glControlGroup)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:gl_control_groups,code,' . $glControlGroup->id,
            'description' => 'nullable|string',
        ]);

        $glControlGroup->update($request->all());

        return redirect()->route('gl-control-groups.index')->with('success', 'GL Control Group updated successfully.');
    }

    public function destroy(GLControlGroup $glControlGroup)
    {
        $glControlGroup->delete();

        return redirect()->route('gl-control-groups.index')->with('success', 'GL Control Group deleted successfully.');
    }
}
