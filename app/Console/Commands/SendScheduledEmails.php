<?php

namespace App\Console\Commands;

use App\Http\Controllers\RobagDataController;
use App\Mail\ReportEmail;
use App\Models\ScheduleEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendScheduledEmails extends Command
{
    protected $signature = 'reports:send-scheduled-emails';
    protected $description = 'Envío de correos programados';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $now = now();
        $scheduledEmails = ScheduleEmail::where('date', '<=', $now)
            ->whereTime('time', '=', $now->format('H:i'))
            ->get();

        foreach ($scheduledEmails as $scheduleEmail) {
            $this->emailReport($scheduleEmail);
        }
    }

    public function emailReport(ScheduleEmail $scheduleEmail)
    {
        $controller = new RobagDataController();

        // Llama al método generateReport con la opción de guardar en storage
        $filePath = $controller->generateReport(true);

        Log::info($filePath);
        Log::info(is_null($filePath));
        if (is_null($filePath)) return;
        
        $subject = $scheduleEmail->subject;
        $description = $scheduleEmail->description;

        // Enviar el correo con el archivo adjunto
        Mail::to($scheduleEmail->main_email)
        ->cc($scheduleEmail->cco)
        ->send(new ReportEmail($subject, $description, $filePath));
        
        // Eliminar el archivo temporal después de enviar el correo
        unlink($filePath);
        Log::info("correos enviados");
    }
}
