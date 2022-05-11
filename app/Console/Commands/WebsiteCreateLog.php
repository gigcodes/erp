<?php

namespace App\Console\Commands;

use App\Http\Controllers\WebsiteLogController;
use App\User;
use DB;
use Exception;
use Illuminate\Console\Command;
use File;

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
    public function __construct(WebsiteLogController $webLog)
    {
        parent::__construct();
        $this->webLog = $webLog;
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
            $this->webLog->store();
            DB::commit();
            echo PHP_EOL . "=====DONE====" . PHP_EOL;
        } catch (Exception $e) {
            echo $e->getMessage();
            DB::rollBack();
            echo PHP_EOL . "=====FAILED====" . PHP_EOL;
        }
    }
}
