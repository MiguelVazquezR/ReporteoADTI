<?php

namespace App\Http\Controllers;

use App\Models\RobagData;
use Carbon\Carbon;
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
        // Aquí se asume que el request tiene un JSON con los datos de la máquina
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

    public function getDataByDateRange(Request $request)
    {   
        $start = Carbon::parse($request->date[0])->toDateTimeString();
        $end = Carbon::parse($request->date[1])->toDateTimeString();
        
        // Ventas y gastos de la semana seleccionada
        $data = RobagData::whereBetween('created_at', [$start, $end])
        ->get();

        return response()->json(compact('data'));
    }
}
