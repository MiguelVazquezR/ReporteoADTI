<?php

namespace App\Http\Controllers;

use App\Mail\ReportEmail;
use Illuminate\Http\Request;
use App\Models\Machine;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
            ->send(new ReportEmail($subject, $description, $filePath, null));
    }

    public function getMetrics()
    {
        $items = [
            $this->getAvailability(),
            $this->getQuality(),
            $this->getPerformance(),
            $this->getOEE(),
        ];

        // devolver en un array los datos obtenidos
        return response()->json(compact('items'));
    }

    public function getAvailability()
    {
        // Consulta SQL para obtener la Disponibilidad
        $query = "
        SELECT
            ROUND(
                (ultimo_registro.run_time * 100.0) / 
                (tiempo_total.tiempo_programado),
            2) AS Disponibilidad
        FROM (
            SELECT
                run_time
            FROM robag1
            WHERE created_at >= NOW() - INTERVAL 8 HOUR
                AND run_time IS NOT NULL
            ORDER BY created_at DESC
            LIMIT 1
        ) AS ultimo_registro,
        (
            SELECT
                COUNT(*) * 300 AS tiempo_programado
            FROM robag1
            WHERE created_at >= NOW() - INTERVAL 8 HOUR
                AND run_time IS NOT NULL
        ) AS tiempo_total;
        ";

        // Ejecutar la consulta
        $disponibilidad = DB::select($query);

        return $disponibilidad;
    }

    public function getQuality()
    {
        // Consulta SQL para obtener la Calidad
        $query = "
        SELECT
            ROUND(
                (ultimo_registro.scale_good_bags * 100.0) / ultimo_registro.total_bags,
            2) AS Calidad
        FROM (
            SELECT
                scale_good_bags,
                total_bags
            FROM robag1
            WHERE created_at >= NOW() - INTERVAL 8 HOUR
                AND scale_good_bags IS NOT NULL
                AND total_bags IS NOT NULL
            ORDER BY created_at DESC
            LIMIT 1
        ) AS ultimo_registro;
        ";

        // Ejecutar la consulta
        $calidad = DB::select($query);

        return $calidad;
    }

    public function getPerformance()
    {
        // Consulta SQL para obtener el Rendimiento
        $query = "
        SELECT
            ROUND(
                (ultimo_registro.full_bags * 100.0) / 
                (200 * (ultimo_registro.run_time / 60)),
            2) AS Rendimiento
        FROM (
            SELECT
                run_time,
                full_bags
            FROM robag1
            WHERE created_at >= NOW() - INTERVAL 8 HOUR
                AND run_time IS NOT NULL
                AND full_bags IS NOT NULL
            ORDER BY created_at DESC
            LIMIT 1
        ) AS ultimo_registro;
        ";

        // Ejecutar la consulta
        $rendimiento = DB::select($query);

        return $rendimiento;
    }

    public function getOEE()
    {
        // Consulta SQL para obtener el OEE
        $query = "
        SELECT
            ROUND(
                ((ultimo_registro.run_time * 1.0) / tiempo_total.tiempo_programado *
                (ultimo_registro.full_bags * 1.0) / (200 * (ultimo_registro.run_time / 60)) *
                (ultimo_registro.scale_good_bags * 1.0) / ultimo_registro.total_bags
            ) * 100, 2) AS OEE
        FROM (
            SELECT
                run_time,
                full_bags,
                scale_good_bags,
                total_bags
            FROM robag1
            WHERE created_at >= NOW() - INTERVAL 8 HOUR
                AND run_time IS NOT NULL
                AND full_bags IS NOT NULL
                AND scale_good_bags IS NOT NULL
                AND total_bags IS NOT NULL
            ORDER BY created_at DESC
            LIMIT 1
        ) AS ultimo_registro,
        (
            SELECT
                COUNT(*) * 300 AS tiempo_programado
            FROM robag1
            WHERE created_at >= NOW() - INTERVAL 8 HOUR
                AND run_time IS NOT NULL
        ) AS tiempo_total;
        ";

        // Ejecutar la consulta
        $oee = DB::select($query);

        return $oee;
    }
}
