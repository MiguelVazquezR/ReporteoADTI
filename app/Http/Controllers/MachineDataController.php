<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Machine;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MachineDataController extends Controller
{
    public function getDataByDateRange(Request $request)
    {
        $subHours = request('subHours');
        $data = $this->getItemsByDateRange($request->date, $subHours);

        return response()->json(compact('data'));
    }
    
    public function pdfTemplate(Request $request)
    {
        $bpm = intval(request('bpm'));
        $dates = request('dates');
        $date = request('date');
        $timeSlots = request('timeSlots');
        $selectedVariables = request('selectedVariables') ?? [];
        $machine = Machine::firstWhere('in_view', true);
        // return compact('bpm', 'dates', 'date', 'timeSlots', 'selectedVariables'); 
        return inertia('Home/Template', compact('bpm', 'dates', 'date', 'timeSlots', 'selectedVariables', 'machine'));
    }

    // funciones privadas
    private function getItemsByDateRange($dates, $subHours = 6)
    {
        $start = Carbon::parse($dates[0])->subHours($subHours)->toDateTimeString();
        $end = Carbon::parse($dates[1])->subHours($subHours)->toDateTimeString();

        // obtener nombre del modelo de la maquina seleccionada
        $machine_selected = Machine::firstWhere('in_view', true);
        $model_class = $machine_selected?->class_name;
        // Ventas y gastos de la semana seleccionada
        $items = $model_class::whereBetween('created_at', [$start, $end])
            ->get();

        return $items;
    }
}
