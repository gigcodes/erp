<?php

namespace App\Console\Commands;

use App\Account;
use App\Setting;
use App\InstagramKeyword;
use Illuminate\Http\Request;
use Illuminate\Console\Command;
use App\Marketing\InstaAccAutomationForm;

class InstaAutoFeedDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'InstaAutoFeedDaily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'InstaAutoFeedDaily';

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
//        $posts = Setting::get('posts_per_day');
//        $likes = Setting::get('likes_per_day');
//        $send_requests = Setting::get('send_requests_per_day');
//        $accept_requests = Setting::get('accept_requests_per_day');
//        $image_requests = Setting::get('image_per_post');
//
        //		// $automation_form = InstaAccAutomationForm::latest()->first();
//        // $posts = $automation_form->posts_per_day;
//        // $likes = $automation_form->likes_per_day;
//        // $send_requests = $automation_form->send_requests_per_day;
//        // $accept_requests = $automation_form->accept_requests_per_day;
//
//        dump(['likes' => $likes, 'posts' => $posts, 'send_requests' => $send_requests, 'accept_requests' => $accept_requests, 'image_requests' =>$image_requests]);
//        while($posts){
//            dump('posts -- start');
//            // $images = $this->getImages();
//            for ($i=1; $i < 5; $i++) {
//                $images = $this->getImagesByKeyword();
//                if ($images != null) {
//                    break;
//                }
//            }
//            while($images == null){
//                $images = $this->getImages();
//            }
//            $imageURI = [];
//            for($i=0; $i< $image_requests-1; $i++){
//                $imageURI[] = $images[$i];
//            }
//            $captions = app('App\Http\Controllers\InstagramPostsController')->getCaptions(new Request());
//            $caption = $captions[rand(0, count($captions) - 1)];
//
//            $post_data = new Request();
//            $post_data->_token = $this->generateRandomString(40);
//            $account = Account::inRandomOrder()->first();
//            $post_data->account_id = $account->id;
//            $post_data->captions = [$caption];
//            $post_data->imageURI = $imageURI;
//            $result = app('App\Http\Controllers\InstagramPostsController')->postMultiple($post_data);
//            dump($result->getData());
//            $posts--;
//        }
//        while($likes){
//            dump('likes -- start');
//            $account = Account::inRandomOrder()->first();
//            $post_data = new Request();
//            $post_data->_token = $this->generateRandomString(40);
//            $post_data->account_id = $account->id;
//            $result= app('App\Http\Controllers\InstagramPostsController')->likeUserPost($post_data);
//            dump($result->getData());
//            $likes--;
//        }
//        while($send_requests){
//            dump('send_requests -- start');
//            $account = Account::inRandomOrder()->first();
//            $post_data = new Request();
//            $post_data->_token = $this->generateRandomString(40);
//            $post_data->account_id = $account->id;
//            $result= app('App\Http\Controllers\InstagramPostsController')->sendRequest($post_data);
//            dump($result->getData());
//            $send_requests--;
//        }
//        while($accept_requests){
//            dump('accept_requests -- start');
//            $account = Account::inRandomOrder()->first();
//            $post_data = new Request();
//            $post_data->_token = $this->generateRandomString(40);
//            $post_data->account_id = $account->id;
//            $result= app('App\Http\Controllers\InstagramPostsController')->acceptRequest($post_data);
//            dump($result->getData());
//            $accept_requests--;
//        }
    }
//
//    function generateRandomKeyword() {
//
//        $keyword = InstagramKeyword::orderBy('id', 'DESC')->first();
//
//        for ($i=0; $i < $keyword->id; $i++) {
//            $random = rand(1,$keyword->id);
//            $keyword = InstagramKeyword::find($random);
//            if ($keyword) {
//                break;
//            }
//        }
//        $keyword = $keyword->keyword;
//        return $keyword;
//    }
//
//    function generateRandomString($length = 10) {
//
//        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
//        $charactersLength = strlen($characters);
//        $randomString = '';
//        for ($i = 0; $i < $length; $i++) {
//            $randomString .= $characters[rand(0, $charactersLength - 1)];
//        }
//        return $randomString;
//    }

//    function getImages($length = 10) {
//        $post_data = new Request();
//        $post_data->type = 'photos';
//        $post_data->keyword = $this->generateRandomString(1);
//        return app('App\Http\Controllers\InstagramPostsController')->getImages($post_data);
//    }

//    function getImagesByKeyword($length = 10) {
//        $post_data = new Request();
//        $post_data->type = 'photos';
//        $post_data->keyword = $this->generateRandomKeyword();
//        return app('App\Http\Controllers\InstagramPostsController')->getImages($post_data);
//    }
}
