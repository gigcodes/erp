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
    public function getSubscriptionReport(Request $request)
    {

      
        $id=0;
        $reports = AppSubscriptionReport::groupBy('start_date')->get();
        return view('appconnect.app-sub',['reports'=>$reports,'id'=>$id]);
   
    }
   
 public function getUsageReportfilter(Request $request)
    {

      
        $reports = AppUsageReport::groupBy('start_date');
        if ($request->input('app_name')) {
            $app_name=$request->input('app_name');
            $reports = $reports->Where('product_id', 'like', '%'. $app_name . '%');
        } 
        if ($request->input('fdate')) {
            $fdate=$request->input('fdate');
            if($request->input('edate'))
            {
             $edate=$request->input('edate');
            }
            else{
             $edate=$fdate  ; 
            }
            // $reports = $reports->Where('start_date', 'like', '%' . $date . '%');
            // $reports = $reports->whereBetween('start_date', ['2023-02-28', '2023-03-09']);
            $reports = $reports->whereDate('start_date', '>=', $fdate)
    ->whereDate('start_date', '<=', $edate);
        } 
     



        $id=0;
        $reports = $reports->get();
        return view('appconnect.app-users',['reports'=>$reports,'id'=>$id]);
   
    }
    public function getRatingsReportfilter(Request $request)
    {
          $reports = AppRatingsReport::groupBy('start_date');
  if ($request->input('app_name')) {
            $app_name=$request->input('app_name');
            $reports = $reports->Where('product_id', 'like', '%'. $app_name . '%');
        } 
      
        $id=0;
      
        if ($request->input('fdate')) {
            $fdate=$request->input('fdate');
            if($request->input('edate'))
            {
             $edate=$request->input('edate');
            }
            else{
             $edate=$fdate ;  
            }
            // $reports = $reports->Where('start_date', 'like', '%' . $date . '%');
            $reports = $reports->whereDate('start_date', '>=', $fdate)
    ->whereDate('start_date', '<=', $edate);
        } 
     



        $id=0;
        $reports = $reports->get();
        return view('appconnect.app-rate',['reports'=>$reports,'id'=>$id]);
   
    }
    public function getSalesReportfilter(Request $request)
    {
          $reports = AppSalesReport::groupBy('start_date');
  if ($request->input('app_name')) {
            $app_name=$request->input('app_name');
            $reports = $reports->Where('product_id', 'like', '%'. $app_name . '%');
        } 
      
        $id=0;
      
        if ($request->input('fdate')) {
            $fdate=$request->input('fdate');
            if($request->input('edate'))
            {
             $edate=$request->input('edate');
            }
            else{
             $edate=$fdate;
            }
            // $reports = $reports->Where('start_date', 'like', '%' . $date . '%');
          $reports = $reports->whereDate('start_date', '>=', $fdate)
    ->whereDate('start_date', '<=', $edate);
        } 
     



        $id=0;
        $reports = $reports->get();
        return view('appconnect.app-sales',['reports'=>$reports,'id'=>$id]);
   
    }
    public function getPaymentReportfilter(Request $request)
    {
   $reports = AppPaymentReport::groupBy('start_date');
        if ($request->input('app_name')) {
            $app_name=$request->input('app_name');
            $reports = $reports->Where('product_id', 'like', '%'. $app_name . '%');
        } 
        $id=0;
     
        if ($request->input('fdate')) {
            $fdate=$request->input('fdate');
            if($request->input('edate'))
            {
             $edate=$request->input('edate');
            }
            else{
             $edate=$fdate;   
            }
            // $reports = $reports->Where('start_date', 'like', '%' . $date . '%');
           $reports = $reports->whereDate('start_date', '>=', $fdate)
    ->whereDate('start_date', '<=', $edate);
        } 
     



        $id=0;
        $reports = $reports->get();
        return view('appconnect.app-pay',['reports'=>$reports,'id'=>$id]);
   
    }
    public function getAdsReportfilter(Request $request)
    {
          $reports = AppAdsReport::groupBy('start_date');
  if ($request->input('app_name')) {
            $app_name=$request->input('app_name');
            $reports = $reports->Where('product_id', 'like', '%'. $app_name . '%');
        } 
      
        $id=0;
      
        if ($request->input('fdate')) {
            $fdate=$request->input('fdate');
            if($request->input('edate'))
            {
             $edate=$request->input('edate');
            }
            else{
             $edate=$fdate;   
            }
            // $reports = $reports->Where('start_date', 'like', '%' . $date . '%');
           $reports = $reports->whereDate('start_date', '>=', $fdate)
    ->whereDate('start_date', '<=', $edate);
        } 
     



        $id=0;
        $reports = $reports->get();
        return view('appconnect.app-ads',['reports'=>$reports,'id'=>$id]);
   
    }
    public function getSubscriptionReportfilter(Request $request)
    {
 $reports = AppSubscriptionReport::groupBy('start_date');
        if ($request->input('app_name')) {
            $app_name=$request->input('app_name');
            $reports = $reports->Where('product_id', 'like', '%'. $app_name . '%');
        } 
        $id=0;
       
        if ($request->input('fdate')) {
            $fdate=$request->input('fdate');
            if($request->input('edate'))
            {
             $edate=$request->input('edate');
            }
            else{
             $edate=$fdate;   
            }
            // $reports = $reports->Where('start_date', 'like', '%' . $date . '%');
            $reports = $reports->whereDate('start_date', '>=', $fdate)
    ->whereDate('start_date', '<=', $edate);
        } 
     



        $id=0;
        $reports = $reports->get();
        return view('appconnect.app-sub',['reports'=>$reports,'id'=>$id]);
   
    }
   

    
}