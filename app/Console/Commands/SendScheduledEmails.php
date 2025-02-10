<?php

namespace App\Console\Commands;

use App\Http\Controllers\PdfController;
use App\Mail\ReportEmail;
use App\Models\ScheduleEmail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendScheduledEmails extends Command
{
    protected $signature = 'reports:send-scheduled-emails';
    protected $description = 'Envío de correos programados';

    public function handle()
    {
        $now = now();
        // Día de la semana en español
        $currentDay = $now->locale('es')->dayName;
        $currentTime = Carbon::parse($now->format('H:i')); // Hora actual en formato 24 horas

        // Verificar conectividad a Internet
        if (!$this->isConnectedToInternet()) {
            Log::warning('No hay acceso a Internet. Abortando envío de correos.');
            return;
        }

        $scheduledEmails = ScheduleEmail::all();

        // si son las 00:00 de hoy, cambiar a null propiedad last_send_at de todos los registros (limpiar envios)
        if ($now->format('H:i') == '13:00') {
            $scheduledEmails->each(fn($schedule) => $schedule->update(['last_send_at' => null]));
            Log::info('Envios limpios');
            return;
        }

        // Filtrar los correos programados que deben enviarse
        $scheduledEmails = $scheduledEmails->filter(function ($email) use ($currentDay, $currentTime) {
            // Verificar si last_send_at es null
            if (is_null($email->last_send_at)) {
                $time = Carbon::parse($email->time);

                // Verificar si la frecuencia es diaria y la hora es igual o menor a la hora actual
                if ($email->frecuency === 'Diariamente' && $time->lessThanOrEqualTo($currentTime)) {
                    return true;
                }

                // Verificar si la frecuencia es semanal, el día es igual al día actual y la hora es igual o menor a la hora actual
                if ($email->frecuency === 'Una vez a la semana' && $email->weekday === $currentDay && $time->lessThanOrEqualTo($currentTime)) {
                    return true;
                }
            }

            return false;
        });

        $counEmails = count($scheduledEmails);
        if ($counEmails > 0) {
            foreach ($scheduledEmails as $email) {
                // ejecutar metodo generateReport() de PdfController para guardar el reporte en la carpeta storage
                $pdfController = new PdfController();
                $pdfPath = $pdfController->generateReport($email->report_name);
                // Enviar el correo electrónico
                Mail::to($email->main_email)
                    ->cc($email->cco)
                    ->send(new ReportEmail($email->subject, $email->description, $pdfPath));

                // Actualizar la propiedad last_send_at
                $email->last_send_at = $now;
                $email->save();

                // eliminar el archivo PDF
                unlink($pdfPath);
            }

            Log::info($counEmails . " Correos enviados");
        }
    }

    /**
     * Verificar si hay acceso a Internet.
     *
     * @return bool
     */
    private function isConnectedToInternet()
    {
        try {
            Http::get('https://www.google.com');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
