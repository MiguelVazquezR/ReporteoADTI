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
        
    }

    public function emailReport(ScheduleEmail $scheduleEmail)
    {
        
    }
}
