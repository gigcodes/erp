<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeleteTotemJobEveryTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'totem-jobs:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Totem Jobs need to delete';

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
        //
        $jobs = \App\Job::where('payload','like',"%Studio\Totem\Events%")->get();
        if(!$jobs->isEmpty()) {
            foreach($jobs as $job) {
                echo $job->id." started to delete \n";
                $jobs->delete();
            }
        }
    }
}
