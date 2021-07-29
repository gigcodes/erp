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
        $websiteStoreView = WebsiteStoreView::whereNotNull("ref_theme_group_id")->update(['ref_theme_group_id' => null]);
        
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
                dump($g->name);
                $all_themes_ids[$g->name] = $g->id;
                if(in_array(str_replace('theme_', '', $g->name), $existing_themes)){
                    $postURL  = 'https://api.livechatinc.com/v3.2/configuration/action/update_group';
                    $postData = [
                        'id' => $g->id,
                        'name' => 'theme_' . str_replace('theme_', '', $g->name),
                        'language_code' => $g->language_code,
                        'agent_priorities' => $g->agent_priorities,
                        'routing_status' => $g->routing_status,
                    ];
                    $postData = json_encode($postData, true);
                    $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, $postData, 'application/json', true, 'POST');
                    if (!isset($response->error)) {
                        $changed_themes[] = 'theme_' . str_replace('theme_', '', $g->name);
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
        dump(['existing_themes_ids' => $existing_themes_ids, 'all_themes_ids' => $all_themes_ids, 'changed_themes' => $changed_themes]);

        // Part - 2

        $ref_themes = [ 
                        "General" => 0,
                        "Lussolicious" => 1,
                        "theme_VeraLusso" => 2,
                        "theme_AvoirChic" => 3,
                        "o-labels.com" => 4,
                        "theme_Sololuxury" => 5,
                        "theme_Suv&Nat" => 6,
                        "theme_Brands & Labels" => 7,
                        "Luxury Space" => 8,
                        "theme_Shades Shop" => 9,
                        "theme_Upeau" => 10,
                        "theme_TheFitEdit" => 11,
                        "Italybrandoutlets" => 12,
                    ];  
        foreach($all_themes_ids as $key => $t){
            if(in_array($key, array_keys($ref_themes))){
                dump($key . ' ref theme');
            }else{
                $group_id = $t;
                $theme_name = substr($key, 0, -3);

                if($theme_name == 'Vera Lusso' || $theme_name == 'VERA LUSSO'){
                    $theme_name = 'theme_VeraLusso';
                }else if($theme_name == 'Brands & Labels'){
                    $theme_name = 'theme_Brands & Labels';
                }else if($theme_name == 'AvoirChic'){
                    $theme_name = 'theme_AvoirChic';
                }else if($theme_name == 'SOLO LUXURY'){
                    $theme_name = 'theme_Sololuxury';
                }else if($theme_name == 'VeraLusso'){
                    $theme_name = 'theme_VeraLusso';
                }else if($theme_name == 'Suv&Nat'){
                    $theme_name = 'theme_Suv&Nat';
                }else if($theme_name == 'o-labels'){
                    $theme_name = 'o-labels.com';
                }else if($theme_name == 'Italy brand outlets'){
                    $theme_name = 'Italybrandoutlets';
                }else if($theme_name == 'Shades Shop'){
                    $theme_name = 'theme_Shades Shop';
                }else if($theme_name == 'TheFitEdit'){
                    $theme_name = 'theme_TheFitEdit';
                }else if($theme_name == 'Upeau'){
                    $theme_name = 'theme_Upeau';
                }else{
                    dd('theme not exist');
                }

                $ref_group_id = $ref_themes[$theme_name];
                $postURL1  = 'https://api.livechatinc.com/v2/properties/group/' . $ref_group_id;
                $result1 = app('App\Http\Controllers\LiveChatController')->curlCall($postURL1, [], 'application/json', true, 'GET');
                $postURL2  = 'https://api.livechatinc.com/v2/properties/group/' . $t;
                $result2 = app('App\Http\Controllers\LiveChatController')->curlCall($postURL2, $result1['response'], 'application/json', true, 'PUT');
                WebsiteStoreView::where('store_group_id', $t)->update(['ref_theme_group_id' => $ref_group_id]);
                dump($key . ' ' . $t . ' '. $theme_name . ' is updated ' .  $ref_group_id);
            }
        }
        dd('ref groups updated');




        // Optional Method
        // $website_store_views = WebsiteStoreView::whereNotNull('store_group_id')->whereNull('ref_theme_group_id')->orderByDesc('id')->get();
        // foreach($website_store_views as $key => $v){
        //     $store_web = StoreWebsite::withTrashed()->find($v->websiteStore->website->store_website_id);
        //     $web_title = $store_web->title;
        //     // dump($web_title);
        //     if($web_title == 'Demo Store'){
        //         continue;
        //     }else if($web_title == 'Vera Lusso' || $web_title == 'VERA LUSSO'){
        //         $web_title = 'theme_VeraLusso';
        //     }else if($web_title == 'Brands & Labels'){
        //         $web_title = 'theme_Brands & Labels';
        //     }else if($web_title == 'AvoirChic'){
        //         $web_title = 'theme_AvoirChic';
        //     }else if($web_title == 'SOLO LUXURY'){
        //         $web_title = 'theme_Sololuxury';
        //     }else if($web_title == 'VeraLusso'){
        //         $web_title = 'theme_VeraLusso';
        //     }else if($web_title == 'Suv&Nat'){
        //         $web_title = 'theme_Suv&Nat';
        //     }else if($web_title == 'o-labels.com'){
        //         $web_title = 'o-labels.com';
        //     }else if($web_title == 'Italybrandoutlets'){
        //         $web_title = 'Italybrandoutlets';
        //     }else if($web_title == 'Shades Shop'){
        //         $web_title = 'theme_Shades Shop';
        //     }else if($web_title == 'TheFitEdit'){
        //         $web_title = 'theme_TheFitEdit';
        //     }else if($web_title == 'Upeau'){
        //         $web_title = 'theme_Upeau';
        //     } 
        //     if(in_array($web_title, array_keys($all_themes_ids))){
        //         $postURL1  = 'https://api.livechatinc.com/v2/properties/group/' . $all_themes_ids[$web_title];
        //         $result1 = app('App\Http\Controllers\LiveChatController')->curlCall($postURL1, [], 'application/json', true, 'GET');
        //         $postURL2  = 'https://api.livechatinc.com/v2/properties/group/' . $v->store_group_id;
        //         $result2 = app('App\Http\Controllers\LiveChatController')->curlCall($postURL2, $result1['response'], 'application/json', true, 'PUT');
        //         WebsiteStoreView::where('id', $v->id)->update(['ref_theme_group_id' => $all_themes_ids[$web_title]]);
        //         dump($v->id . ' ' . $web_title . ' is updated ' .  $all_themes_ids[$web_title]);
        //     }else{
        //         dump($v->id . ' ' . $web_title . ' is not updated ');
        //     }
        // }





    }
}
