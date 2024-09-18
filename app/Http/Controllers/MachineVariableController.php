<?php

namespace App\Http\Controllers;

use App\Models\MachineVariable;
use Illuminate\Http\Request;

class MachineVariableController extends Controller
{
    public function index()
    {
        $variables = MachineVariable::where('machine_name', 'Robag')->get();

        return inertia('MachineVariable/Index', compact('variables'));
    }

    public function create()
    {
        return inertia('MachineVariable/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'machine_name' => 'required|string|max:255',
            'variable_name' => 'required|string|max:255',
            'variable_description' => 'nullable|string|max:800',
            'variable_address' => 'required|numeric|max:65535',
            'type' => 'required|string|max:255',
            'words' => 'required|numeric|min:1',
        ]);

        MachineVariable::create($validated);

        return to_route('machine-variables.index');
    }

    public function show(MachineVariable $machineVariable)
    {
        //
    }

    public function edit(MachineVariable $machineVariable)
    {
        return inertia('MachineVariable/Edit', [
            'variable' => $machineVariable,
        ]);
    }

    public function update(Request $request, MachineVariable $machineVariable)
    {
        $validated = $request->validate([
            'machine_name' => 'required|string|max:255',
            'variable_name' => 'required|string|max:255',
            'variable_description' => 'nullable|string|max:800',
            'variable_address' => 'required|numeric|max:65535',
            'type' => 'required|string|max:255',
            'words' => 'required|numeric|min:1',
        ]);

        $machineVariable->update($validated);

        return to_route('machine-variables.index');
    }

    public function destroy(MachineVariable $machineVariable)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->items_ids as $id) {
            // evitar eliminar al usuario autenticado
            if ($id != auth()->id()) {
                $item = MachineVariable::find($id);
                $item?->delete();
            }
        }
    }

    public function getVariables($machine)
    {
        $items = MachineVariable::where('machine_name', $machine)->get();

        return response()->json(compact('items'));
    }
}
