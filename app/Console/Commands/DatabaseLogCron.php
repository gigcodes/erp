<?php

namespace App\Console\Commands;

use App\DatabaseLog;
use App\SlowLogsEnableDisable;
use Illuminate\Console\Command;

class DatabaseLogCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'databaselog:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */

    public function handle()
    {
        $namefile = storage_path('logs/mysql/server_audit.log');
        if (!empty($namefile)) {
            $lines = @file($namefile);
            if ($lines) {
                for ($i = count($lines) - 1; $i >= 0; $i--) {
                    DatabaseLog::create(['logmessage' => $lines[$i]]);
                }
                return 'Database Log Inserted Successfully';
            }
            return 'File not found!';
        }
    }
}
