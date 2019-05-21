<?php

namespace App\Console\Commands;

use App\Account;
use App\Brand;
use App\HashTag;
use App\HashtagPostHistory;
use App\InstagramAutomatedMessages;
use App\Services\Instagram\Hashtags;
use Illuminate\Console\Command;
use InstagramAPI\Instagram;

class AutoReplyOnSololuxuryHashtags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hashtags:reply-sololuxury-instagram';

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
        $hashtag = HashTag::where('hashtag', 'sololuxuryindia')->first();
        if (!$hashtag) {
            $hashtag = new HashTag();
            $hashtag->hashtag = 'sololuxuryindia';
            $hashtag->rating = 10;
            $hashtag->save();
        }

        $counter = 1;

        $hashtags = new Hashtags();
        $hashtags->login();
        $cursor = '';


        do {
            $postsAll = $hashtags->getFeed($hashtag->hashtag);


            [$posts, $cursor] = $postsAll;

            foreach ($posts as $post) {

                if ($counter >= 10) {
                    break;
                }

                $mediaId = $post['media_id'];
                $code = $post['code'];
                $usernameToIgnore = HashtagPostHistory::where('account_id')->take(2)->get()->pluck('account_id')->toArray();
                $account = Account::where('platform', 'instagram')
                    ->whereNotIn('id', $usernameToIgnore)
//                    ->inRandomOrder()
                    ->first();

                echo "$account->last_name \n" ;


                $message = InstagramAutomatedMessages::where('type', 'text')
                    ->where('sender_type', 'normal')
                    ->where('receiver_type', 'hashtag')
                    ->where('status', '1')
                    ->orderBy('use_count', 'ASC')
                    ->first();



                $instagram = new Instagram();
                $instagram->login($account->last_name, $account->password);
                $instagram->media->comment($mediaId, $message->message);

                ++$message->use_count;
                $message->status = 2;
                $message->save();

                $postHistory = new HashtagPostHistory();
                $postHistory->hashtag = 'sololuxury';
                $postHistory->account_id = $account->id;
                $postHistory->instagram_automated_message_id = $message->id;
                $postHistory->post_id = $code;
                $postHistory->cursor = $cursor;
                $postHistory->post_date = date('Y-m-d');
                $postHistory->type = 'comment';
                $postHistory->save();

                $counter++;

            }

        } while($cursor != 'END' && $counter <=10);

    }
}
