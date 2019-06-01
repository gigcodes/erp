<?php

namespace App\Console\Commands;

use App\Account;
use App\AutoCommentHistory;
use App\AutoReplyHashtags;
use App\Brand;
use App\HashTag;
use App\HashtagPostHistory;
use App\InstagramAutoComments;
use App\InstagramAutomatedMessages;
use App\Services\Instagram\Hashtags;
use Illuminate\Console\Command;
use InstagramAPI\Instagram;

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
        $hashtag = AutoReplyHashtags::where('status', 1)->first();

        $counter = 0;

        $hashtags = new Hashtags();
        $hashtags->login();
        $cursor = '';

        $commentCount = 0;

        do {

            [$posts, $cursor] = $hashtags->getFeed($hashtag->text, $cursor);


            foreach ($posts as $post) {

                $comment = InstagramAutoComments::inRandomOrder()->first();
                $account = Account::where('platform', 'instagram')->where('broadcast', 0)->inRandomOrder()->first();

                if (!isset($this->accounts[$account->id])) {
                    $ig = new Instagram();
                    $ig->login($account->last_name, $account->password);
                    $this->accounts[$account->id] = $ig;
                }

                $this->accounts[$account->id]->media->comment($post['media_id'], $comment->comment);


                $history = new AutoCommentHistory();
                $history->account_id = $account->id;
                $history->comment = $comment->comment;
                $history->target = $hashtag->text;
                $history->post_code = $post['code'];
                $history->post_id = $post['media_id'];
                $history->auto_reply_hashtag_id = $hashtag->id;
                $history->save();


                sleep(5);
            }

            $counter++;




        } while($cursor != 'END' && $counter <=50);

        $hashtag->status = 0;
        $hashtag->save();

    }
}
