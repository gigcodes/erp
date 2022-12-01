<?php

namespace App\Console\Commands;

use App\DeveloperTask;
//use App\Services\Instagram\Hashtags;
use Illuminate\Console\Command;

//use InstagramAPI\Instagram;

class ChangeTesterBasedOnTeamLead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ChangeTesterBasedOnTeamLead';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $accounts = [];

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
        DeveloperTask::where('team_lead_id', 319)->update(['tester_id' => 414]);
    }
}
