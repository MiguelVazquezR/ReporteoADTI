<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DBBackup extends Command
{
    protected $signature = 'app:db-backup';

    protected $description = 'Respaldo de la base de datos de Reporteo';

    public function handle()
    {
        $databaseName = env('DB_DATABASE');
        $databaseUser = env('DB_USERNAME');
        $databasePassword = env('DB_PASSWORD');
        $backupDate = now()->format('j_F_Y');

        //obtener carpeta Documents de cualquier usuario y concatenar la ruta de la carpeta de backups
        $databaseFolder = getenv('USERPROFILE') . "\\Documents\\DTWbackups\\Reporteo";
        $databaseBackupPath = $databaseFolder . "\\db_$backupDate.sql";

        //revisar si la carpeta de backups existe o si no crearla
        if (!file_exists($databaseFolder)) {
            mkdir($databaseFolder, 0777, true);
        }

        $mysqldumpPath = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
        $command = "\"{$mysqldumpPath}\" --user={$databaseUser} --password={$databasePassword} {$databaseName} > \"{$databaseBackupPath}\"";

        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            $this->info('Backup realizado.');
            Log::info("Backup realizado.");
        } else {
            $this->error('Backup fallido.');
            Log::error("Backup fallido.");
        }
    }
}
