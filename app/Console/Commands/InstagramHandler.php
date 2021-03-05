<?php

namespace App\Console\Commands;
use App\Account;
use App\Http\Controllers\InstagramPostsController;
use Illuminate\Console\Command;

class InstagramHandler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instagram:handler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'post images, likes, send request, accept request for instagram account';

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
        $this->info('test');
        $query = Account::query();
        $accounts = $query->orderBy('id','desc')->get();
        foreach($accounts as $key=>$account)
        {
        //    if($key > 0)
        //        continue;
           // $this->info($account->id);
           $myRequest = new \Illuminate\Http\Request();
            $myRequest->setMethod('POST');
            $myRequest->request->add(['account_id' => $account->id]);
            $this->info($myRequest->account_id);
            $InstagramPostsController = new InstagramPostsController();
            $InstagramPostsController->likeUserPost($myRequest);
            $InstagramPostsController->sendRequest($myRequest);
            $InstagramPostsController->acceptRequest($myRequest);
            $get_images = $InstagramPostsController->getImages();
            $get_caption = $InstagramPostsController->getCaptions();
            $selected_images = [];
            $selected_caption = [];
            if(!empty($get_caption)){
                $selected_caption[] = $get_caption[0]['id'];
            }
            $images_selected_no = 2;
            foreach($get_images as $key=>$images){
                if($key <= ($images_selected_no-1)){
                    //$this->info($images);
                    $selected_images[] = $images;
                }
            }
            $myRequest->request->add(['imageURI' => $selected_images]);
            $myRequest->request->add(['captions' => $selected_caption]);
            $InstagramPostsController->postMultiple($myRequest);
           // $this->info($selected_images);
            
        }

    }
}
