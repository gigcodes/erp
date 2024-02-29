<?php

namespace App\Console\Commands;

use App\CronJob;
use Carbon\Carbon;
use App\CronJobReport;
use App\Social\SocialConfig;
use App\Services\Facebook\FB;
use Illuminate\Console\Command;

class SyncInstagramPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instagram:sync-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to sync all the posts from instagram that is added in the config';

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
     * @return void
     */
    public function handle()
    {
        try {
            $report = CronJobReport::create([
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $configs = SocialConfig::where([
                'platform' => 'instagram',
                'status'   => 1,
            ])->get();

            foreach ($configs as $config) {
                $fb = new FB($config->page_token);

                $posts = $fb->getInstagramPosts($config->account_id);

                foreach ($posts['posts']['media'] as $post) {
                    $config->posts()->updateOrCreate(['ref_post_id' => $post['id']], [
                        'post_body'   => $post['caption'] ?? '',
                        'post_by'     => $post['owner']['id'],
                        'posted_on'   => Carbon::parse($post['timestamp']),
                        'status'      => 1,
                        'permalink'   => $post['permalink'],
                        'image_path'  => $post['media_url'],
                        'media'       => $post['children'] ?? null,
                        'custom_data' => [
                            'like_count'         => $post['like_count'],
                            'comments_count'     => $post['comments_count'],
                            'media_product_type' => $post['media_product_type'],
                            'media_type'         => $post['media_type'],
                        ],
                    ]);
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
