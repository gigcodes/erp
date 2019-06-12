<?php

namespace App\Console\Commands;

use App\HashTag;
use App\Services\Instagram\Hashtags;
use Illuminate\Console\Command;

class AddHahstagPostCountWeekly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hashtags:update-counts';

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
        $hashtags = HashTag::orderBy('post_count', 'ASC')->get();
        $ht = new Hashtags();
        $ht->login();

        foreach ($hashtags as $hashtag) {
            $count = $ht->getMediaCount($hashtag->hashtag);
            $hashtag->post_count = $count;
            $hashtag->save();
            sleep(5);
        }
    }
}
