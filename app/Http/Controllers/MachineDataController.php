<?php

namespace App\Http\Controllers;

use App\Mail\ReportEmail;
use Illuminate\Http\Request;
use App\Models\Machine;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

    public function emailReport(Request $request)
    {
        $validatedData = $request->validate([
            'main_email' => 'required|email',
            'cco' => 'nullable|array',
            'cco.*' => 'nullable|email', // Cada CCO debe ser un correo válido
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Recoger los datos validados
        $mainEmail = $validatedData['main_email'];
        $cco = $validatedData['cco'] ?? [];
        $subject = $validatedData['subject'];
        $description = $validatedData['description'] ?? '';

        // Generar el reporte excel y guardar en storage
        // $filePath = $this->generateReport(true, $request->dates); // Guardar el archivo y obtener la ruta

        $filePath = $request->pdf_path;
        // Enviar el correo con el archivo adjunto
        Mail::to($mainEmail)
            ->cc($cco)
            ->send(new ReportEmail($subject, $description, $filePath));
    }
}
