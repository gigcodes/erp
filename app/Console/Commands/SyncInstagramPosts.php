<?php

namespace App\Console\Commands;

use App\CronJob;
use Carbon\Carbon;
use App\CronJobReport;
use App\Social\SocialConfig;
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
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $configs = SocialConfig::where([
                'platform' => 'instagram',
                'status' => 1,
            ])->get();

            foreach ($configs as $config) {
                $pageInfoParams = [ // endpoint and params for getting page
                    'endpoint_path' => $config->page_id,
                    'fields' => 'business_discovery.username(' . $config->account_id . '){media{id,caption,like_count,comments_count,timestamp,media_product_type,media_type,owner,permalink,media_url,children{media_url}}}',
                    'access_token' => $config->page_token,
                    'request_type' => 'GET',
                ];

                $pageInfo = getFacebookResults($pageInfoParams);

                $posts = $pageInfo['data']['business_discovery']['media']['data'];

                foreach ($posts as $post) {
                    $config->posts()->updateOrCreate(['ref_post_id' => $post['id']], [
                        'post_body' => $post['caption'] ?? '',
                        'post_by' => $config->page_id,
                        'ref_post_id' => $post['owner']['id'],
                        'posted_on' => Carbon::parse($post['timestamp']),
                        'status' => 1,
                        'permalink' => $post['permalink'],
                        'image_path' => $post['media_url'],
                        'media' => isset($post['children']) ? $post['children']['data'] : null,
                        'custom_data' => [
                            'like_count' => $post['like_count'],
                            'comments_count' => $post['comments_count'],
                            'media_product_type' => $post['media_product_type'],
                            'media_type' => $post['media_type'],
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
