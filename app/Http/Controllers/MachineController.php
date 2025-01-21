<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        $machines = Machine::all();
        return inertia('Machines/Create', compact('machines'));
    }

    public function store(Request $request)
    {
        // Validar los datos recibidos
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:machines',
            'class_name' => 'required|string|max:255|unique:machines',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Manejar la subida de la imagen
        if ($request->hasFile('image')) {
            // guardar el path en carpeta storage/app/machines y en la base de datos
            $path = $request->file('image')->store('machines', 'public');
            $validatedData['image'] = $path;
        }

        // Crear una nueva máquina
        $validatedData['class_name'] = "App\Models\\{$validatedData['class_name']}";
        $machine = Machine::create($validatedData);

        // clonar las variables de la máquina seleccionada si se ha seleccionado
        if ($request->machine_id_to_clone_vars) {
            $machineToClone = Machine::find($request->machine_id_to_clone_vars);
            $machineToClone->variables->each(function ($variable) use ($machine) {
                $machine->variables()->create($variable->toArray());
            });
        }

        return to_route('home');
    }

    public function show(Machine $machine)
    {
        //
    }

    public function edit(Machine $machine)
    {
        return inertia('Machines/Edit', [
            'machine' => $machine,
        ]);
    }

    public function update(Request $request, Machine $machine)
    {
        // Validar los datos recibidos
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:machines,name,' . $machine->id,
            'class_name' => 'required|string|max:255|unique:machines,class_name,' . $machine->id,
            // 'image' => 'nullable|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Manejar la subida de la imagen
        if ($request->hasFile('image')) {
            // guardar el path en carpeta storage/app/machines y en la base de datos
            $path = $request->file('image')->store('machines', 'public');
            $validatedData['image'] = $path;
        }

        // Actualizar la máquina
        $validatedData['class_name'] = "App\Models\\{$validatedData['class_name']}";
        $machine->update($validatedData);

        return to_route('home');
    }

    public function destroy(Machine $machine)
    {
        //
    }

    public function updateInView(Machine $machine)
    {
        $currentMachine = Machine::firstWhere('in_view', true);
        if ($currentMachine) {
            $currentMachine->update(['in_view' => false]);
        }
        $machine->update(['in_view' => true]);
    }
}
