<?php

namespace App\Console\Commands;

use App\Account;
use Carbon\Carbon;
use App\CronJobReport;
use App\AutoReplyHashtags;
use App\AutoCommentHistory;
use Illuminate\Console\Command;

class AutoCommentBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instagram:auto-comment-hashtags';

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
        try {
            $report = CronJobReport::create([
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $hashtag = AutoReplyHashtags::where('status', 1)->first();

            $counter = 0;

            $hashtags = new Hashtags();
            $hashtags->login();
            $cursor = '';

            $commentCount = 0;

            $posts = AutoCommentHistory::where('status', 1)->take(50)->get();

            foreach ($posts as $post) {
                $country = $post->country;

                $account = Account::where('platform', 'instagram')->where('bulk_comment', 1);

                if (strlen($country) >= 4) {
                    $account = $account->where(function ($q) use ($country) {
                        $q->where('country', $country)->orWhereNull('country');
                    });
                }

                $caption = $post->caption;
                $caption = str_replace(['#', '@', '!', '-' . '/'], ' ', $caption);
                $caption = explode(' ', $caption);

                $account = $account->inRandomOrder()->first();

                $this->accounts[$account->id]->media->comment($post->post_id, $comment->comment);

                $post->status     = 0;
                $post->account_id = $account->id;
                $post->comment    = $comment->comment;
                $post->save();

                sleep(5);
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
