<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StoreWebsiteAnalytic;
use App\StoreWebsite;
use Illuminate\Support\Facades\Validator;

class StoreWebsiteAnalyticsController extends Controller
{

    public function __construct()
    {
        // $this->middleware(function ($request, $next) {
        //     session()->flash('active_tab','blogger_list_tab');
        //     return $next($request);
        // });
    }

    public function index()
    {
        $storeWebsiteAnalyticsData = StoreWebsiteAnalytic::all();
        return view('store-website-analytics.index', compact('storeWebsiteAnalyticsData'));
    }

    public function create(Request $request)
    {   if($request->post()){
        $rules = [
                'website' => 'required',
                'account_id' => 'required',
                'view_id' => 'required',
                'store_website_id' => 'required|integer',
            ];

            $messages = [
       'website' => 'Website field is required.',
       'account_id' => 'Account Id field is required.',
       'view_id' => 'View Id field is required.',
       'store_website_id' => 'Store Id field is required.',
       'store_website_id' => 'Store Id value must be a number.',
   ];

   $validation = validator(
       $request->all(),
       $rules,
       $messages
   );

   //If validation fail send back the Input with errors
     if($validation->fails()) {
         //withInput keep the users info
         return redirect()->back()->withErrors($validation)->withInput();
     } else {
         if($request->id){
             $updatedData = $request->all();
             unset($updatedData['_token']);
             StoreWebsiteAnalytic::whereId($request->id)->update($updatedData);
             return redirect()->to('/store-website-analytics/index')->with('success','Store Website Analytics updated successfully.');
         }else{
             StoreWebsiteAnalytic::create($request->all());
             return redirect()->to('/store-website-analytics/index')->with('success','Store Website Analytics saved successfully.');
         }
     }
    }else{
        $storeWebsites = StoreWebsite::where('deleted_at',null)->get();
        return view('store-website-analytics.create',compact('storeWebsites'));
    }

    }

    public function edit($id = null)
    {
        $storeWebsiteAnalyticData = StoreWebsiteAnalytic::whereId($id)->first();
        $storeWebsites = StoreWebsite::where('deleted_at',null)->get();
        return view('store-website-analytics.edit',compact('storeWebsiteAnalyticData','storeWebsites'));

    }

    public function delete($id = null)
    {
        StoreWebsiteAnalytic::whereId($id)->delete();
        return redirect()->to('/store-website-analytics/index')->with('success','Record deleted successfully.');
    }

}
