<?php

namespace App\Console\Commands;

use App\Jobs\SendScheduledEmail;
use App\Models\ScheduleEmail;
use Illuminate\Console\Command;

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
            SendScheduledEmail::dispatch($scheduleEmail);
        }
    }
}
