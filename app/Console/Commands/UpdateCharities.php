<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Vendor;
use App\VendorCategory;
use App\CustomerCharity;

class UpdateCharities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateCharities';

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
        $vendor_category = VendorCategory::where('title', 'charity')->first();
        if($vendor_category){
            $vendors = Vendor::where('category_id', $vendor_category->id)->get()->toArray();
            foreach($vendors as $v){
                CustomerCharity::insert($v);
            }
        }else{
            dump('charity category not exist!');
        }
    }
}
