<?php

namespace App\Http\Controllers;

use App\Models\ScheduleEmail;
use Illuminate\Http\Request;

class ScheduleEmailController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        return inertia('Setting/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'main_email' => 'required|email',
            'cco' => 'nullable|array',
            'cco.*' => 'nullable|email', // Cada CCO debe ser un correo válido
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'frecuency' => 'required|string',
            'time' => 'required|string',
        ]);

        ScheduleEmail::create($request->all());

        return to_route('home');
    }

    public function show(ScheduleEmail $scheduleEmail)
    {
        //
    }

    public function edit($schedule_email)
    {
        $schedule_email = ScheduleEmail::find($schedule_email);

        return inertia('Setting/Edit', compact('schedule_email'));
    }

    public function update(Request $request, $schedule_email)
    {
        $request->validate([
            'main_email' => 'required|email',
            'cco' => 'nullable|array',
            'cco.*' => 'nullable|email', // Cada CCO debe ser un correo válido
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'frecuency' => 'required|string',
            'time' => 'required|string',
        ]);
        $schedule_email = ScheduleEmail::find($schedule_email);
        $schedule_email->update($request->all());

        return to_route('home');
    }

    public function destroy(ScheduleEmail $scheduleEmail)
    {
        //
    }
}
