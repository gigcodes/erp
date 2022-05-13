<?php

namespace App\Console\Commands;

use App\Http\Controllers\WebsiteLogController;
use App\User;
use DB;
use Exception;
use Illuminate\Console\Command;
use File;
use App\StoreWebsite;
use App\WebsiteLog;


class WebsiteCreateLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:websitelog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create website error log from database file';

    public $webLog;
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
        //dd( $this->webLog->store());
        //
        try {
            DB::beginTransaction();
            $mainPath = env('WEBSITES_LOGS_FOLDER');
            $ifPathExist = file_exists($mainPath);
            if($ifPathExist){
                $this->info("Found Path");
                $filesDirectories = scandir($mainPath);
                foreach($filesDirectories as $websiteName) {
                    // find the Directory
                    if(File::isDirectory($mainPath) && $websiteName != '.' && $websiteName != '..'){
                        $website = StoreWebsite::select('website')->where('website',  'like', '%' . $websiteName. '%')->first();
                        if(!$website){
                            $this->info("Website not found ".$websiteName);
                        }else{
                            $fullPath = File::allFiles($mainPath);
                            foreach ($fullPath as $key => $val) {

                                if(file_exists($mainPath.$websiteName.'/'.$val->getFilename()) && $val->getFilename() == 'db.log')
                                {
                                    if($val->getFilename() == 'db.log')
                                        $fileTypeName = 'db';
                                    else   
                                        $fileTypeName = $val->getFilename();
                                    $content = File::get($mainPath.$websiteName.'/'.$val->getFilename());
                                    $logs = preg_split('/\n\n/', $content);
                                    $totalLogs = [];
                                    foreach ($logs as $log) {
                                        $entries = explode(PHP_EOL, $log);
                                        $sql = null;
                                        $time = null;
                                        $module = null;

                                        foreach ($entries as $entry) {
                                            $this->info("DB log not found ".$entry);

                                            if (strpos($entry, 'SQL') !== false) {
                                                $sql = str_replace('SQL:','',$entry);
                                            }
                                            if (strpos($entry, 'TIME') !== false) {
                                                $time = str_replace('TIME:','',$entry);
                                            }
                                            if (strpos($entry, '#8') !== false) {
                                                $module = str_replace('#8:','',$entry);
                                            }
                                            if(!is_null($sql) && !is_null($time) && !is_null($module)){
                                                $find = WebsiteLog::where([['sql_query', '=', $sql],['time','=',$time],['module', '=', $module]])->first();
                                               if(empty($find)){
                                                    $ins = new WebsiteLog;
                                                    $ins->sql_query = $sql;
                                                    $ins->time = $time;
                                                    $ins->module = $module;
                                                    $ins->website_id = $website->website ?? '';
                                                    $ins->type = $fileTypeName;
                                                    $ins->save();
                                                } 
                                            }

                                        }
                                    }
                                }else{
                                    $this->info("DB log not found ".$websiteName);
                                }
                            }  
                        }
                        
                    }
                }    
            }else{
                $this->info("Cannot find the logs folder");

            }
            DB::commit();
            echo PHP_EOL . "=====DONE====" . PHP_EOL;
        } catch (Exception $e) {
            echo $e->getMessage();
            DB::rollBack();
            echo PHP_EOL . "=====FAILED====" . PHP_EOL;
        }
    }
}
