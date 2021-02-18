<?php

namespace App\Http\Controllers;


use App\StoreWebsiteAnalytic;

class ErpLogController extends Controller
{

    public function test() {

        $data = StoreWebsiteAnalytic::all()->toArray();
        print_r($data); die;
    //     $erpData = [

    //         'model_id' => 1,
    //         'url'      => 'www.google.com',
    //         'model'    => 'erplog',
    //         'request'  => 'sample_req',
    //         'response' => 'sample_res',
    //     ];

    //     storeERPLog($erpData);

    }

}
