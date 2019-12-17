<?php

namespace App\Console\Commands;
use Storage;
use File;
use App\LaravelLog;

use Illuminate\Console\Command;

class ParseLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:log';

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
       $path = storage_path('logs'); 
       $logs =  File::allfiles($path);
       foreach ($logs as $log) {
        $filename = $log->getFilename();
        $content = File::get($log);
        preg_match_all("/\[(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})\](.*)/", $content, $match);
        
        foreach ($match[0] as $value) {
            preg_match_all("/\[([^\]]*)\]/", $value, $datetime);
            $value = str_replace($datetime[1][0], '', $value);
            $value = str_replace('[]', '', $value);
            $dateTime = \Carbon\Carbon::parse($datetime[1][0]);
            $alreadyLogged = LaravelLog::whereDate('log_created',$dateTime)->first();
            //dd($alreadyLogged);
            if($alreadyLogged != null && $alreadyLogged != ''){
                continue;
            }
            $log = new LaravelLog();
            $log->log_created =  $dateTime;
            $log->filename =  $filename;
            $log->log = $value;
            $log->save();
        }
        
       }
    }
}
