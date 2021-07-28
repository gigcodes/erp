<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\WebsiteStoreView;
use App\DatabaseTableHistoricalRecord; 
use App\StoreWebsite;

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
        $all_themes_ids = [];
        
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
                $all_themes_ids[$g->name] = $g->id;
                // if(in_array(str_replace('theme_', '', $g->name), $existing_themes)){
                //     $existing_themes_ids['theme_' . $g->name] = $g->id;
                //     $postURL  = 'https://api.livechatinc.com/v3.2/configuration/action/update_group';
                //     $postData = [
                //         'id' => $g->id,
                //         'name' => 'theme_' . str_replace('theme_', '', $g->name),
                //         'language_code' => $g->language_code,
                //         'agent_priorities' => $g->agent_priorities,
                //         'routing_status' => $g->routing_status,
                //     ];
                //     $postData = json_encode($postData, true);
                //     $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, $postData, 'application/json', true, 'POST');
                //     if (!isset($response->error)) {
                //         $changed_themes[] = 'theme_' . $g->name;
                //         $existing_themes_ids[$g->name] = $g->id;
                //     } 
                // } 
            } 
            if (isset($response->error)) {
                dump(['status' => 'errors', $response], 403);
            } else {
                dump(['status' => 'success', 'responseData' => $changed_themes], 200);
            }
        } 
        dump($all_themes_ids);

        // Part - 2

        $website_store_views = WebsiteStoreView::whereNotNull('store_group_id')->whereNull('ref_theme_group_id')->get();
        foreach($website_store_views as $key => $v){
            $store_web = StoreWebsite::withTrashed()->find($v->websiteStore->website->store_website_id);
            $web_title = $store_web->title;
            if($web_title == 'Demo Store'){
                continue;
            }else if($web_title == 'VERA LUSSO'){
                $web_title = 'Vera Lusso';
            }
            $code =  explode('-', $v->code)[1];
            $web_name = $store_web->title . '_' . $code;
            $postURL1  = 'https://api.livechatinc.com/v2/properties/group/' . $all_themes_ids[$web_name];
            $result1 = app('App\Http\Controllers\LiveChatController')->curlCall($postURL1, [], 'application/json', true, 'GET');
            $postURL2  = 'https://api.livechatinc.com/v2/properties/group/' . $v->store_group_id;
            $result2 = app('App\Http\Controllers\LiveChatController')->curlCall($postURL2, $result1['response'], 'application/json', true, 'PUT');
            WebsiteStoreView::where('id', $v->id)->update(['ref_theme_group_id' => $all_themes_ids[$web_name]]);
            dump($v->id . ' ' . $web_name . ' is updated ' .  $all_themes_ids[$web_name]);
        }
    }


}
