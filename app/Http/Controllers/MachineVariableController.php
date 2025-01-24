<?php

namespace App\Http\Controllers;

use App\Models\MachineVariable;
use App\Models\Machine;
use App\Models\RobagData;
use Illuminate\Http\Request;

class MachineVariableController extends Controller
{
    public function index()
    {
        $machine_in_view = Machine::firstWhere('in_view', true);
        $variables = MachineVariable::where('machine_id', $machine_in_view?->id)->get();

        return inertia('MachineVariable/Index', compact('variables', 'machine_in_view'));
    }

    public function create()
    {
        return inertia('MachineVariable/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // 'machine_id' => 'required|numeric|exists:machines,id',
            'name' => 'required|string|max:255',
            'original_name' => 'required|string|max:255|unique:machine_variables',
            'description' => 'nullable|string|max:800',
            'address' => 'required|numeric|max:65535',
            'type' => 'required|string|max:255',
            'words' => 'required|numeric|min:1',
        ]);

        $machine_in_view = Machine::firstWhere('in_view', true);

        MachineVariable::create($validated + ['machine_id' => $machine_in_view?->id]);

        // Limpiar la caché relacionada con las variables de esta máquina
        cache()->forget('modbus_variables_' . $machine_in_view?->id);

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
        // Validar los datos recibidos
        $validated = $request->validate([
            // 'machine_id' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'original_name' => 'required|string|max:255|unique:machine_variables,original_name,' . $machineVariable->id,
            'description' => 'nullable|string|max:800',
            'address' => 'required|numeric|max:65535',
            'type' => 'required|string|max:255',
            'words' => 'required|numeric|min:1',
            'is_active' => 'required|boolean',
        ]);

        // Verificar si el nombre de la variable ha cambiado (solo para información en json)
        // if ($machineVariable->name !== $validated['name']) {
        //     // Obtener el antiguo nombre de la variable
        //     $oldName = $machineVariable->name;

        //     // Actualizar la variable en MachineVariable
        //     $machineVariable->update($validated);

        //     // Buscar todos los registros de RobagData que contienen la variable con el nombre antiguo
        //     $robagDataRecords = RobagData::where('data', 'like', '%"' . $oldName . '"%')->get();

        //     // Recorrer cada registro y actualizar el nombre de la variable en el JSON
        //     foreach ($robagDataRecords as $record) {
        //         $data = $record->data; // Acceder al campo JSON 'data'

        //         // Verificar si el antiguo nombre existe en el JSON
        //         if (array_key_exists($oldName, $data)) {
        //             // Cambiar el nombre de la clave en el JSON
        //             $data[$validated['name']] = $data[$oldName];
        //             unset($data[$oldName]); // Eliminar la clave con el nombre antiguo

        //             // Guardar el registro con el nombre actualizado
        //             $record->data = $data;
        //             $record->save();
        //         }
        //     }
        // } else {
        //     // Si no ha cambiado el nombre, simplemente actualizar los campos de MachineVariable
        //     $machineVariable->update($validated);
        // }

        $machine_in_view = Machine::firstWhere('in_view', true);

        // Actualizar la variable
        $machineVariable->update($validated + ['machine_id' => $machine_in_view?->id]);
        
        // Limpiar la caché relacionada con las variables de esta máquina
        cache()->forget('modbus_variables_' . $machine_in_view?->id);

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

        // Limpiar la caché relacionada con las variables de esta máquina
        cache()->forget('modbus_variables_' . $item->machine_id);
    }

    public function massiveToggleStatus(Request $request)
    {
        foreach ($request->items_ids as $id) {
            $item = MachineVariable::find($id);
            $item?->update(['is_active' => !$item->is_active]);
        }

        // Limpiar la caché relacionada con las variables de esta máquina
        cache()->forget('modbus_variables_' . $item->machine_id);
    }

    public function getVariables()
    {
        $MachineInView = Machine::firstWhere('in_view', true);
        $items = MachineVariable::where([
            'machine_id' => $MachineInView?->id,
            'is_active' => true
        ])->get();

        return response()->json(compact('items'));
    }
}
