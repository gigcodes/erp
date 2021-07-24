<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Marketing\InstaAccAutomationForm;
use App\Account;

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
		$automation_form = InstaAccAutomationForm::latest()->first();
        $posts = $automation_form->posts_per_day;
        $likes = $automation_form->likes_per_day;
        $send_requests = $automation_form->send_requests_per_day;
        $accept_requests = $automation_form->accept_requests_per_day;
        dump(['likes' => $likes, 'posts' => $posts, 'send_requests' => $send_requests, 'accept_requests' => $accept_requests]);
        while($posts){  
            dump('posts -- start');
            $images = $this->getImages();
            while($images == null){
                $images = $this->getImages();
            }
            $imageURI = [];
            for($i=0; $i< rand(1, count($images) - 1); $i++){
                $imageURI[] = $images[$i];
            }
            $captions = app('App\Http\Controllers\InstagramPostsController')->getCaptions(new Request());   
            $caption = $captions[rand(0, count($captions) - 1)]; 

            $post_data = new Request();
            $post_data->_token = $this->generateRandomString(40);
            $account = Account::inRandomOrder()->first();
            $post_data->account_id = $account->id; 
            $post_data->captions = [$caption];
            $post_data->imageURI = $imageURI;
            $result = app('App\Http\Controllers\InstagramPostsController')->postMultiple($post_data); 
            dump($result->getData());
            $posts--;
        }
        while($likes){
            dump('likes -- start');
            $account = Account::inRandomOrder()->first();
            $post_data = new Request();
            $post_data->_token = $this->generateRandomString(40);
            $post_data->account_id = $account->id; 
            $result= app('App\Http\Controllers\InstagramPostsController')->likeUserPost($post_data);   
            dump($result->getData());
            $likes--;
        }
        while($send_requests){
            dump('send_requests -- start');
            $account = Account::inRandomOrder()->first();
            $post_data = new Request();
            $post_data->_token = $this->generateRandomString(40);
            $post_data->account_id = $account->id; 
            $result= app('App\Http\Controllers\InstagramPostsController')->sendRequest($post_data);   
            dump($result->getData());
            $send_requests--;
        }
        while($accept_requests){
            dump('accept_requests -- start');
            $account = Account::inRandomOrder()->first();
            $post_data = new Request();
            $post_data->_token = $this->generateRandomString(40);
            $post_data->account_id = $account->id; 
            $result= app('App\Http\Controllers\InstagramPostsController')->acceptRequest($post_data);   
            dump($result->getData());
            $accept_requests--;
        }
    } 

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    function getImages($length = 10) {
        $post_data = new Request();
        $post_data->type = 'photos';
        $post_data->keyword = $this->generateRandomString(1);
        return app('App\Http\Controllers\InstagramPostsController')->getImages($post_data); 
    }
}
