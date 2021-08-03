<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\StoreWebsite;
use Illuminate\Support\Facades\DB;

class AddRoutesToGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AddRoutesToGroups';

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

        // Part-1
        // $existing_routes = DB::table('group_routes')->get();
        // foreach($existing_routes as $r){
        //     dump($r->route_id);
        //     $postURL  = 'https://api.livechatinc.com/v3.3/configuration/action/delete_auto_access';
        
        //     $postData = [
        //         "id" => $r->route_id
        //     ];
        //     $postData = json_encode($postData, true);
        //     $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, $postData, 'application/json', true, 'POST');
        //     dump($result);
        // }
        // dd('routes deleted');
        // Part-2 Create routes and update langauages to group
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
            $existing_themes = ['General', 'Lussolicious', 'o-labels.com', 'Luxury Space', 'Italybrandoutlets', 'AvoirChic', 'Brands & Labels', 'Shades Shop', 'Sololuxury', 'VeraLusso', 'Suv&Nat', 'TheFitEdit', 'Upeau']; 
            $changed_themes = [];
            foreach($response as $g){
                $all_themes_ids[$g->name] = $g->id;
                if(!in_array(str_replace('theme_', '', $g->name), $existing_themes)){
                    $data = explode('_', $g->name);
                    if(count($data) !=2){
                        continue;
                    }
                    $lang_code = $data[1]; 
                    // Update language to group
                    $postURL  = 'https://api.livechatinc.com/v2/properties/group/' . $g->id;
                    $postData = [
                        'language' => $lang_code,
                    ];
                    $postData = json_encode($postData, true);
                    $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, $postData, 'application/json', true, 'PUT');
                    $response = json_decode($result['response']);
                    if (!isset($response->error)) {
                        dump($g->id . ' ' . $g->name . ' == ' . $lang_code . ' lang updated.');
                    }else{
                        dump([$g->id . ' ' . $g->name . ' == ' . $lang_code . ' lang error.' , $response]);
                    }
                    //Create route fo group
                    $postURL  = 'https://api.livechatinc.com/v3.3/configuration/action/add_auto_access';
                    $domain_values["value"] =  '-' . $data[1];
                    $url_values["value"] = $data[0];
                    $postData = [
                        'description' => $g->name,
                        'access' => [
                            'groups' => [$g->id]
                        ],
                        'conditions' => [
                            'domain' => [
                                'values' => [$domain_values]
                            ],
                            'url' => [
                                'values' => [$url_values]
                            ]
                        ]
                    ]; 
                    $postData = json_encode($postData, true);
                    $result = app('App\Http\Controllers\LiveChatController')->curlCall($postURL, $postData, 'application/json', true, 'POST');
                    $response = json_decode($result['response']);
                    dump($response);
                    if (!isset($response->error)) {
                        dump($g->id . ' ' . $g->name . ' == ' . $lang_code . ' route updated.');
                        DB::table('group_routes')->updateOrInsert([
                            'group_id' => $g->id,
                        ],[
                            'group_id' => $g->id,
                            'route_id' => $response->id
                        ]);
                    }   
                }else{
                    dump($g->name . ' skipped');
                } 
            } 
            if (isset($response->error)) {
                dump(['status' => 'errors', $response], 403);
            } else {
                dump(['status' => 'success', 'responseData' => $changed_themes], 200);
            }
        } 
        // dump(['existing_themes_ids' => $existing_themes_ids, 'all_themes_ids' => $all_themes_ids, 'changed_themes' => $changed_themes]);

    }
}
