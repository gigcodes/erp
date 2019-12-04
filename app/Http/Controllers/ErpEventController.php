<?php

namespace App\Http\Controllers;
use App\ErpEvents;

class ErpEventController extends Controller
{

    public function index()
    {
        echo '<pre>'; print_r(266); echo '</pre>';exit;
    }

    public function dummy()
    {
        $params = [
            "event_name" => "Testing Event",
            "event_description" => "This is test description",
            "start_date" => "2019-12-04",
            "end_date" => "2019-12-15",
            "type" => "1",
            "brand_id" => "1,2,3",
            "category_id" => "10,38",
            "number_of_person" => "20",
            "product_start_date" => "",
            "product_end_date" => "",
            "minute" => "0",
            "hour" => "1",
            "day_of_month" => "0",
            "month" => "0",
            "day_of_week" => "0",
            "created_by" => "1"
        ];

        $erpEvnts  = new ErpEvents();
        $erpEvnts->fill($params);
        $erpEvnts->save();    

    }



}
