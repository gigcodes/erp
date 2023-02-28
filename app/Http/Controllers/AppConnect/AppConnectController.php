<?php

namespace App\Http\Controllers\AppConnect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Google\Client;
use Illuminate\Support\Facades\Auth;
use App\AppUsageReport;
use App\AppSubscriptionReport;
use App\AppSalesReport;
use App\AppRatingsReport;
use App\AppPaymentReport;
use App\AppAdsReport;

class AppConnectController extends Controller
{

 
    public function getUsageReport()
    {

      
        $id=0;
        $reports = AppUsageReport::groupBy('start_date')->get();
        return view('appconnect.app-users',['reports'=>$reports,'id'=>$id]);
   
    }
    public function getRatingsReport()
    {

      
        $id=0;
        $reports = AppRatingsReport::groupBy('start_date')->get();
        return view('appconnect.app-rate',['reports'=>$reports,'id'=>$id]);
   
    }
    public function getSalesReport()
    {

      
        $id=0;
        $reports = AppSalesReport::groupBy('start_date')->get();
        return view('appconnect.app-sales',['reports'=>$reports,'id'=>$id]);
   
    }
    public function getPaymentReport()
    {

      
        $id=0;
        $reports = AppPaymentReport::groupBy('start_date')->get();
        return view('appconnect.app-pay',['reports'=>$reports,'id'=>$id]);
   
    }
    public function getAdsReport()
    {

      
        $id=0;
        $reports = AppAdsReport::groupBy('start_date')->get();
        return view('appconnect.app-ads',['reports'=>$reports,'id'=>$id]);
   
    }
    public function getSubscriptionReport()
    {

      
        $id=0;
        $reports = AppSubscriptionReport::groupBy('start_date')->get();
        return view('appconnect.app-sub',['reports'=>$reports,'id'=>$id]);
   
    }
   


    
}