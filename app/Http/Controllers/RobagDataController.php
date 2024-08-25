<?php

namespace App\Http\Controllers;

use App\Models\RobagData;
use Illuminate\Http\Request;

class RobagDataController extends Controller
{

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        // Aquí asumes que el request tiene un JSON con los datos de la máquina
        $data = $request->all();

        // Guardar los datos en la base de datos
        RobagData::create($data);

        // Responder con un mensaje de éxito
        return response()->json(['message' => 'Datos guardados correctamente'], 200);
    }

    public function show(RobagData $robagData)
    {
        //
    }

    public function edit(RobagData $robagData)
    {
        //
    }

    public function update(Request $request, RobagData $robagData)
    {
        //
    }

    public function destroy(RobagData $robagData)
    {
        //
    }
}
