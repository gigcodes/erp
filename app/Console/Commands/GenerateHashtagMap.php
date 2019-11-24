<?php

namespace App\Console\Commands;

use App\Services\Explorer\InstagramExplorer;
use Illuminate\Console\Command;


class GenerateHashtagMap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hahstag:generate-map';

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
        $report = CronJobReport::create([
        'signature' => $this->signature,
        'start_time'  => Carbon::now()
        ]);

        $explorer = new InstagramExplorer();
        $explorer->loginToInstagram();
        $explorer->getSimilarHashtags();

        $report->update(['end_time' => Carbon:: now()]);
    }
}
