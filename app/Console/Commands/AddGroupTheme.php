<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\WebsiteStoreView;
use App\DatabaseTableHistoricalRecord; 

class AddGroupTheme extends Command
{ 
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AddGroupTheme';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'AddGroupTheme';

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
        $existing_themes_ids = [];
        
        // Part - 1

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
            $existing_themes = ['AvoirChic', 'Brands & Labels', 'Shades Shop', 'Sololuxury', 'VeraLusso', 'Suv&Nat', 'TheFitEdit', 'Upeau']; 
            $changed_themes = [];
            foreach($response as $g){
                if(in_array(str_replace('theme_', '', $g->name), $existing_themes)){
                    $existing_themes_ids['theme_' . $g->name] = $g->id;
                    $postURL  = 'https://api.livechatinc.com/v3.2/configuration/action/update_group';
                    $postData = [
                        'id' => $g->id,
                        'name' => 'theme_' . $g->name,
                        'name' => 'theme_' . $g->name,
                        'language_code' => $g->language_code,
                        'agent_priorities' => $g->agent_priorities,
                        'routing_status' => $g->routing_status,
                    ];
                    $postData = json_encode($postData, true);
                    $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, $postData, 'application/json', true, 'POST');
                    if (!isset($response->error)) {
                        $changed_themes[] = 'theme_' . $g->name;
                        $existing_themes_ids[$g->name] = $g->id;
                    } 
                } 
            } 
            if (isset($response->error)) {
                dump(['status' => 'errors', $response], 403);
            } else {
                dump(['status' => 'success', 'responseData' => $changed_themes], 200);
            }
        } 

        // Part - 2

        $website_store_views = WebsiteStoreView::whereNotNull('store_group_id')->whereNull('ref_theme_group_id')->get();
        foreach($website_store_views as $key => $v){
            $web_title = $v->websiteStore->website->storeWebsite->title;
            if($web_title == 'Demo Store'){
                continue;
            }else if($web_title == 'VERA LUSSO'){
                $web_title = 'VeraLusso';
            }else if($web_title == 'SOLO LUXURY'){
                $web_title = 'Sololuxury';
            }
            dump([$existing_themes_ids['theme_' . $web_title], $v->store_group_id]);

            $postURL1  = 'https://api.livechatinc.com/v2/properties/group/' . $existing_themes_ids['theme_' . $web_title];
            $result1 = app('App\Http\Controllers\LiveChatController')->curlCall($postURL1, [], 'application/json', true, 'GET');
            $postURL2  = 'https://api.livechatinc.com/v2/properties/group/' . $v->store_group_id;
            $result2 = app('App\Http\Controllers\LiveChatController')->curlCall($postURL2, $result1['response'], 'application/json', true, 'PUT');
            
            WebsiteStoreView::where('id', $v->id)->update(['ref_theme_group_id' => $existing_themes_ids['theme_' . $web_title]]);
            dump($v->id . ' is updated ' .  $existing_themes_ids['theme_' . $web_title]);
        }
    }


}
