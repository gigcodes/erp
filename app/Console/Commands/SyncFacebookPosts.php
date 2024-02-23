<?php

namespace App\Console\Commands;

use App\CronJob;
use Carbon\Carbon;
use App\CronJobReport;
use App\Social\SocialConfig;
use App\Services\Facebook\FB;
use Illuminate\Console\Command;

class SyncFacebookPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook:sync-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches all the posts for the facebook account';

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
     * Get the facebook posts for all the socials account added and that
     * are active
     *
     * @return void
     */
    public function handle()
    {
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $configs = SocialConfig::where([
                'platform' => 'facebook',
                'status' => 1,
            ])->get();

            foreach ($configs as $config) {
                $fb = new FB($config->page_token);
                $pageInfo = $fb->getPageFeed($config->page_id);

                $posts = $pageInfo['feed'];

                foreach ($posts as $post) {
                    $config->posts()->updateOrCreate(['ref_post_id' => $post['id']], [
                        'post_body' => $post['message'] ?? '',
                        'post_by' => $config->page_id,
                        'ref_post_id' => $post['id'],
                        'posted_on' => Carbon::parse($post['created_time']),
                        'status' => 1,
                    ]);
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
