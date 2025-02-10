<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\ScheduleEmail;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ScheduleEmailController extends Controller
{
    public function index()
    {
        $schedules = ScheduleEmail::all();
        $dashboards = $this->getDashboards();

        return inertia('ScheduleEmail/Index', compact('schedules', 'dashboards'));
    }

    public function create()
    {
        $dashboards = $this->getDashboards();

        return inertia('ScheduleEmail/Create', compact('dashboards'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'main_email' => 'required|email',
            'report_name' => 'required|string|max:255',
            'cco' => 'nullable|array',
            'cco.*' => 'nullable|email', // Cada CCO debe ser un correo válido
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string|max:900',
            'frecuency' => 'required|string',
            'weekday' => 'nullable|string|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',
            'time' => 'required|string',
        ]);

        ScheduleEmail::create($request->all());

        return to_route('schedule-email-settings.index');
    }

    public function show(ScheduleEmail $scheduleEmail)
    {
        //
    }

    public function edit($schedule)
    {
        $schedule = ScheduleEmail::find($schedule);
        $dashboards = $this->getDashboards();

        return inertia('ScheduleEmail/Edit', compact('schedule', 'dashboards'));
    }

    public function update(Request $request, $schedule_email)
    {
        $request->validate([
            'main_email' => 'required|email',
            'report_name' => 'required|string|max:255',
            'cco' => 'nullable|array',
            'cco.*' => 'nullable|email', // Cada CCO debe ser un correo válido
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string|max:900',
            'frecuency' => 'required|string',
            'weekday' => 'nullable|string|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',
            'time' => 'required|string',
        ]);

        $schedule_email = ScheduleEmail::find($schedule_email);
        $schedule_email->update($request->all() + ['last_send_at' => null]);

        return to_route('schedule-email-settings.index');
    }

    public function destroy(ScheduleEmail $scheduleEmail)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->items_ids as $id) {
            $item = ScheduleEmail::find($id);
            $item?->delete();
        }
    }

    public function massiveUpdate(Request $request)
    {
        foreach ($request->selections as $schedule) {
            $schedule = ScheduleEmail::find($schedule['id']);
            $schedule?->update([
                'report_name' => $request->report_name,
                'cco' => $request->cco,
                'time' => $request->time,
            ]);
        }
    }

    // privados
    private function getDashboards()
    {
        $apiKey  = env('METABASE_API_KEY');
        $baseUrl = env('METABASE_API_URL');
        $client  = new Client();

        // obtener lista de nombres de reportes o dashboards de metabase
        $response = $client->request('GET', $baseUrl . '/dashboard', [
            'headers' => ['x-api-key' => $apiKey]
        ]);
        $dashboards = json_decode($response->getBody(), true);

        // obtener array de nombres de reportes
        $dashboards = array_map(function ($dashboard) {
            return $dashboard['name'];
        }, $dashboards);

        return $dashboards;
    }
}
