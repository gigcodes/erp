<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Setting;
use App\StoreViewsGTMetrix;
use App\StoreGTMetrixAccount;
use App\WebsiteStoreView;
use App\StoreWebsite;
use Entrecore\GTMetrixClient\GTMetrixClient;
use Entrecore\GTMetrixClient\GTMetrixTest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class GTMetrixDataToNotQueue extends Command{
     
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:gt_metrix_data_to_not_queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'GT Metrix Data To Not Queue';

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
    public function handle(){

    	$query = StoreViewsGTMetrix::select(\DB::raw('store_views_gt_metrix.*'));
		$lists = $query->from(\DB::raw('(SELECT MAX( id) as id, status, store_view_id, website_url, html_load_time FROM store_views_gt_metrix  GROUP BY store_views_gt_metrix.website_url ) as t'))->leftJoin('store_views_gt_metrix', 't.id', '=', 'store_views_gt_metrix.id')->get();
        if($lists){
            foreach ($lists as $key => $list) {
                if($list->status == '' || $list->status == 'completed'){

                    $gtmetrix = StoreViewsGTMetrix::where('id',$list->id)->first();
                    $update = [
                                'test_id' => null,
                                'status'  => 'not_queued',
                            ];
                    $gtmetrix->update($update);
                }
            }
            \Log::info('GTMetrix :: successfully');
        }

    }
    

}
