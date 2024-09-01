<?php

namespace App\Jobs;

use App\Http\Controllers\RobagDataController;
use App\Mail\ReportEmail;
use App\Models\ScheduleEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendScheduledEmail implements ShouldQueue
{
    use Queueable;
    // use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ScheduleEmail $scheduleEmail)
    {
    }

    public function handle(): void
    {
        $controller = new RobagDataController();

        // Llama al método generateReport con la opción de guardar en storage
        $filePath = $controller->generateReport(true, $this->scheduleEmail->dates);

        $subject = $this->scheduleEmail->subject;
        $description = $this->scheduleEmail->description;

        // Enviar el correo con el archivo adjunto
        Mail::to($this->scheduleEmail->main_email)
            ->cc($this->scheduleEmail->cco)
            ->send(new ReportEmail($subject, $description, $filePath));

        // Eliminar el archivo temporal después de enviar el correo
        unlink($filePath);
    }
}
