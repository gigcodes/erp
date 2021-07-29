<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\WebsiteStoreView;
use App\StoreWebsite;

class UpdateGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateGroups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete old groups and add new groups.';

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
        // Total groups
        // $row = [];
        // $websiteStoreView = WebsiteStoreView::all();
        // foreach($websiteStoreView as $w){
        //     if($w->websiteStore == null){
        //         continue;
        //     }
        //     if($w->websiteStore->website == null){
        //         continue;
        //     }
        //     if($w->websiteStore->website->storeWebsite == null){
        //         continue;
        //     } 
        //     if($w->code == 1){
        //         continue;
        //     }
        //     $web_title = $w->websiteStore->website->storeWebsite->title;
        //     $code =  explode('-', $w->code)[1];
        //     $web_name = $web_title . '_' . $code;
        //     in_array($web_name, $row) ? '' : $row[] = $web_name;
        // }
        // dd($row);

        //Delete Old Groups
        $existing_themes_ids = [];
        $websiteStoreView = WebsiteStoreView::whereNotNull("store_group_id")->update(['store_group_id' => null]);
        $postURL  = 'https://api.livechatinc.com/v3.2/configuration/action/list_groups';
        $postData = [
            "fields" => ["agent_priorities", "routing_status"]
        ];
        $postData = json_encode($postData, true);
        $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, $postData, 'application/json', true, 'POST');
        if ($result['err']) {
            dump(['status' => 'errors', 'errorMsg' => $result['err']], 403);
        } else {
            $response = json_decode($result['response']);
            $existing_themes = ['General', 'AvoirChic', 'Brands & Labels', 'Shades Shop', 'ShadesShop', 'Sololuxury', 'VeraLusso', 'Suv&Nat', 'TheFitEdit', 'Upeau',  'o-labels.com', 'Luxury Space', 'TheFitEdit', 'Italybrandoutlets', 'Lussolicious']; 
            foreach($response as $g){
                if(in_array(str_replace('theme_', '', $g->name), $existing_themes)){
                   dump($g->name . ' not deleted');
                }else{
                    $postURL  = 'https://api.livechatinc.com/v3.2/configuration/action/delete_group';

                    $postData = [
                        'id' => $g->id, 
                    ];
                    $postData = json_encode($postData, true);
                    $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, $postData, 'application/json', true, 'POST');

                    if ($result['err']) {
                        return response()->json(['status' => 'errors', 'errorMsg' => $result['err']], 403);
                    } else {
                        $response = json_decode($result['response']);
                        if (isset($response->error)) {
                            return response()->json(['status' => 'errors', $response], 403);
                        } else {
                            dump($g->name . ' ' . $g->id .  ' deleted');
                        }
                    } 
                }   
            } 
            if (isset($response->error)) {
                dump(['status' => 'errors', $response], 403);
            } else {
                dump(['status' => 'success', 'responseData' => []], 200);
            }
        } 
        dump('gropud deete part -- ended');

        // Create New Groups
        $group_array = [];
        $websiteStoreViews = WebsiteStoreView::get();
        foreach($websiteStoreViews as $w){
            if($w->websiteStore == null){
                continue;
            }
            if($w->websiteStore->website == null){
                continue;
            }
            if($w->websiteStore->website->store_website_id == null){
                continue;
            } 
            if($w->code == 1){
                continue;
            } 
            $web_title = $w->websiteStore->website->store_website_id;
            $store_web = StoreWebsite::withTrashed()->find($w->websiteStore->website->store_website_id);
            $code =  explode('-', $w->code)[1];
            $web_name = $store_web->title . '_' . $code;
            
            if(in_array($web_name, array_keys($group_array))){
                dump($web_name . ' group already exist');
                continue;
            }
            $postURL  = 'https://api.livechatinc.com/v3.2/configuration/action/create_group';
    
            $postData = [
                'name' => $web_name, 
                'agent_priorities' => [
                    'buying@amourint.com' => 'normal'
                ]
            ];
    
            $postData = json_encode($postData, true);
            $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, $postData, 'application/json', true, 'POST');
            if ($result['err']) {
                dump(['name' =>  $web_name, 'status' => 'errors', 'errorMsg' => $result['err']], 403);
            } else {
                $response = json_decode($result['response']);
                if (isset($response->error)) {
                    dump(['name' =>  $web_name, 'status' => 'errors', $response], 403);
                } else {
                    $websiteStoreView = WebsiteStoreView::where("id", $w->id)->first();
                    $group_array[$web_name] = $response->id;
                    if ($websiteStoreView) {
                        $websiteStoreView->store_group_id = $response->id; 
                        $websiteStoreView->save();
                    }
                    dump(['name' =>  $web_name, 'status' => 'success', 'responseData' => $response, 'code' => 200], 200);
                }
            } 
        }
        dump($group_array);


        //Assign Groups to stores
        $websiteStoreViews = WebsiteStoreView::whereNull('store_group_id')->get();
        foreach($websiteStoreViews as $w){
            if($w->websiteStore == null){
                continue;
            }
            if($w->websiteStore->website == null){
                continue;
            }
            if($w->websiteStore->website->store_website_id == null){
                continue;
            } 
            if($w->code == 1){
                continue;
            } 
            $web_title = $w->websiteStore->website->store_website_id;
            $store_web = StoreWebsite::withTrashed()->find($w->websiteStore->website->store_website_id);
            $code =  explode('-', $w->code)[1];
            $web_name = $store_web->title . '_' . $code;
            dump($web_name);
            if(in_array($web_name, array_keys($group_array))){
                $websiteStoreView = WebsiteStoreView::where("id", $w->id)->update(['store_group_id' => $group_array[$web_name]]);
                dump(['websiteStoreView-id' =>  $w->id]);
            }else{
                continue;
            }
        }


        //Assign Groups to remained store          
        $websiteStoreViews = WebsiteStoreView::whereNull('store_group_id')->get();
        foreach($websiteStoreViews as $w){
            if($w->websiteStore == null){
                continue;
            }
            if($w->websiteStore->website == null){
                continue;
            }
            if($w->websiteStore->website->store_website_id == null){
                continue;
            } 
            if($w->code == 1){
                continue;
            } 
            $web_title = $w->websiteStore->website->store_website_id;
            $store_web = StoreWebsite::withTrashed()->find($w->websiteStore->website->store_website_id);
            $code =  explode('-', $w->code)[1];
            $web_name = ucwords(strtolower($store_web->title)) . '_' . $code;
            if(in_array($web_name, array_keys($group_array))){

                $existing_themes_ids = [];
                $postURL  = 'https://api.livechatinc.com/v3.2/configuration/action/list_groups';
                $postData = [
                    "fields" => ["agent_priorities", "routing_status"]
                ];
                $postData = json_encode($postData, true);
                $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, $postData, 'application/json', true, 'POST');
                
                if ($result['err']) {
                    dump(['status' => 'errors', 'errorMsg' => $result['err']], 403);
                } else {
                    $response = json_decode($result['response']);
                    foreach($response as $g){
                        if($g->name == $web_name){
                            $websiteStoreView = WebsiteStoreView::where("id", $w->id)->update(['store_group_id' => $g->id]);
                            dump([$g->name, $w->id, $g->id]);
                        }
                    }  
                } 
            }else{
                dump($w->id);
                continue;
            }

        }

        
    }
}
