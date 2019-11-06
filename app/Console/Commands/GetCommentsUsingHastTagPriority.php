<?php

namespace App\Console\Commands;

use App\HashTag;
use App\HashtagPosts;
use App\InstagramPosts;
use App\InstagramPostsComments;
use App\Keywords;
use App\CronJobReport;
use App\Services\Instagram\Hashtags;
use App\Services\Instagram\Nationality;
use Carbon\Carbon;
use Illuminate\Console\Command;
use InstagramAPI\Instagram;

class GetCommentsUsingHastTagPriority extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hastag:instagram {hastagId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Getting hashtag comments from instagram using hashtag id';

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
         $hashtagId = $this->argument('hastagId');

          $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time'  => Carbon::now()
            ]);


        $hashtag = HashTag::where('id',$hashtagId)->first();

        if (!$hashtag) {
            return;
        }
        $hashtagId = $hashtag->id;
        if($hashtag->priority == 1){
            $sendWhatsApp = 1;
        }else{
            $sendWhatsApp = 0;
        }
       

        $hashtagText = $hashtag->hashtag;

        $hash = new Hashtags();
        $hash->login();
        $maxId = '';
        


        $keywords = Keywords::get()->pluck('text')->toArray();
       
        do {
            $hashtagPostsAll = $hash->getFeed($hashtagText, $maxId);
            [$hashtagPosts, $maxId] = $hashtagPostsAll;

            foreach ($hashtagPosts as $hashtagPost) {
                $location = $hashtagPost['location'];
                
                if(is_array($location)){
                    $location_field = $location['name'];
                }else{
                    $location_field = '';
                 }
                
                $code = $hashtagPost['code'];
                if($code != null && $code != ''){
                    //Check if Hashtag is on Priority
                     if($sendWhatsApp == 1){
                        //CHeck if post exist
                        $postCheck = $hashtagPost['media_id'];
                        $checkIfExist = InstagramPosts::where('post_id', $postCheck)->first();
                        if($checkIfExist == null && $checkIfExist == ''){
                            //Send Whats App With Link
                            $phone = \Config('instagram.number_to_send');
                            $link = 'https://www.instagram.com/p/'.$code;
                            $message = 'New Post On Instagram Please Visit Link  '.$link;
                             app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($phone, '',$message, '', '');
                            }
                        }
                        
                }
                $comments = $hashtagPost['comments'] ?? [];

                if ($comments === []) {
                    continue;
                }

                $comments = $hash->instagram->media->getComments($hashtagPost['media_id'])->asArray();

                $comments = $comments['comments'];

                foreach ($comments as $comment) {
                    $commentText = $comment['text'];
                    
                    foreach ($keywords as $keyword) {
                        if (strpos($commentText, $keyword) !== false) {
                            $postId = $hashtagPost['media_id'];
                           
                            $media = InstagramPosts::where('post_id', $postId)->first();
                            
                            if (!$media) {
                                $media = new InstagramPosts();
                            }


                            $media->post_id = $postId;
                            $media->caption = $hashtagPost['caption'];
                            $media->user_id = $hashtagPost['user_id'];
                            $media->username = $hashtagPost['username'];
                            $media->media_type = $hashtagPost['media_type'];
                            $media->code = $code;
                            $media->location = $location_field;
                            $media->hashtag_id = $hashtagId;
                            
                            if (!is_array($hashtagPost['media'])) {
                                $hashtagPost['media'] = [$hashtagPost['media']];
                            }

                            $media->media_url = $hashtagPost['media'];
                            $media->posted_at = $hashtagPost['posted_at'];
                            $media->save();
                           
                              
                            $commentEntry = InstagramPostsComments::where('comment_id', $comment['pk'])->where('user_id', $comment['user']['pk'])->first();

                            if (!$commentEntry) {
                                $commentEntry = new InstagramPostsComments();
                            }

                            $commentEntry->user_id = $comment['user']['pk'];
                            $commentEntry->name = $comment['user']['full_name'];
                            $commentEntry->username = $comment['user']['username'];
                            $commentEntry->instagram_post_id = $media->id;
                            $commentEntry->comment_id = $comment['pk'];
                            $commentEntry->comment = $comment['text'];
                            $commentEntry->profile_pic_url = $comment['user']['profile_pic_url'];
                            $commentEntry->posted_at = Carbon::createFromTimestamp($comment['created_at'])->toDateTimeString();
                            $commentEntry->save();
                            dump('Comment Stored');
                            
                        }
                    }

                }

            }
        } while ($maxId != 'END');

        $hashtag->is_processed = 1;
        $hashtag->save();

        $report->update(['end_time' => Carbon:: now()]);

         
        
    }
}
