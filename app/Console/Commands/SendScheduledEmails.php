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

        // Obtener el fin del día de ayer
        $yesterdayEnd = $now->copy()->subDay()->endOfDay();

        foreach ($scheduledEmails as $schedule) {
            // Verificar si last_send_at es una instancia de Carbon y si es de ayer o antes
            if ($schedule->last_send_at && $schedule->last_send_at->lte($yesterdayEnd)) {
                $schedule->update(['last_send_at' => null]);
            }
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

                // generar pdf con dompdf y blade
                // $pdfPath = $pdfController->generateReportPDF($email->report_name);

                // generar excel
                $excelPath = $pdfController->generateReportExcel($email->report_name);

                // si $excelPath es diferente de null, quiere decir que hay datos para mostrar en el reporte,
                // por lo tanto se procede a generar PDF
                if ($excelPath) {
                    $description = $email->descriotion;
                    // Ruta del PDF
                    $pdfPath = base_path('reporte.pdf'); // Ubicado en la raíz del proyecto

                    // Ejecutar el script de Node.js con la URL como argumento
                    $command = "node generarPdf.js " . "\"$email->report_name\"";
                    exec($command, $output, $returnVar);

                    if ($returnVar !== 0) {
                        Log::error('Error al generar el PDF con Puppeteer. ' . $output);
                        continue;
                    }
                } else {
                    $pdfPath = null;
                    $description = "No se encontraron datos para mostrar en el reporte de las últimas 8 horas.
                                           Es posible que la máquina esté desconectada o que no se haya iniciado el 
                                           servicio de generación de reportes (Metabase)";
                }

                try {
                    // Enviar el correo electrónico
                    Mail::to($email->main_email)
                        ->send(new ReportEmail($email->subject, $description, $excelPath, $pdfPath));
                } catch (\Exception $e) {
                    Log::error('Error al enviar el correo a ' . $email->main_email . ': ' . $e->getMessage());
                }

                // Enviar el correo electrónico a las copias
                foreach ($email->cco as $cco) {
                    try {
                        Mail::to($cco)
                            ->send(new ReportEmail($email->subject, $description, $excelPath, $pdfPath));
                    } catch (\Exception $e) {
                        Log::error('Error al enviar el correo a ' . $cco . ': ' . $e->getMessage());
                    }
                }
                // Actualizar la propiedad last_send_at
                $email->update(['last_send_at' => $now]);

                // Eliminar los archivos generados
                if (file_exists($pdfPath)) {
                    unlink($pdfPath);
                }

                if (file_exists($excelPath)) {
                    unlink($excelPath);
                }
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
