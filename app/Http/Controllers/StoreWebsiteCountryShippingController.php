<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StoreWebsiteAnalytic;
use App\StoreWebsite;
use App\StoreWebsitesCountryShipping;
use App\SimplyDutyCountry;
use Illuminate\Support\Facades\Validator;
use Storage;
use File;

class StoreWebsiteCountryShippingController extends Controller
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
        try {
            $dataList = StoreWebsitesCountryShipping::all();
            return view('store-website-country-shipping.index', compact('dataList'));
        } catch (Exception $e) {
            \Log::error('Shipping page ::'. $e->getMessage());
        }
    }

    public function create(Request $request)
    {   
        try {
            if($request->post()){
                $rules = [
                    'store_website_id' => 'required',
                    'country_name' => 'required',
                    'country_code' => 'required',
                    'price' => 'required|integer',
                    'currency' => 'required'
                ];


                $messages = [
                   'store_website_id' => 'Website field is required.',
                   'country_name' => 'Country name field is required.',
                   'country_code' => 'Country code field is required.',
                   'price' => 'Price field is required.',
                   'currency' => 'Price field is required.'
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

                    $storeWebsites = StoreWebsite::where('id',$request->store_website_id)->first();

                    $url     = $storeWebsites->website.'/default/rest/all/V1/shippingcost/';
                    $url     = 'https://stage.suvandnat.com/default/rest/all/V1/shippingcost/';
                    $api_key = $storeWebsites->api_token;

                    $headers = [
                        'Authorization' => 'Bearer ' . $api_key,
                        'Content-Type' => 'application/json'
                    ];

                    $pushMagentoArr = array(
                        'shippingCountryCode' => $request->country_code,
                        'shippingCountryName' => $request->country_name,
                        'shippingPrice' => $request->price,
                        'shippingCurrency' => $request->currency,
                    );

                    if($request->id){
                        $updatedData = $request->all();
                        unset($updatedData['_token']);
                        StoreWebsitesCountryShipping::whereId($request->id)->update($updatedData);
                        if( $request->ship_id ){
                            $url .= 'update';
                            $pushMagentoArr['ship_id'] = $request->ship_id;
                            $pushMagentoArr['updatedShippingPrice'] = $request->price;
                            $response = \App\Helpers\GuzzleHelper::post($url,$pushMagentoArr,$headers);
                            
                        }
                        return redirect()->route('store-website-country-shipping.index')->with('success','Data updated successfully.');
                    }else{
                        $insertData = $request->all();
                        $url .= 'add';
                        $response = \App\Helpers\GuzzleHelper::post($url,$pushMagentoArr,$headers);

                        if ( isset($response[0]->status) ) {
                            $insertData['ship_id'] = $response[0]->ship_id;
                            StoreWebsitesCountryShipping::create($insertData);
                            return redirect()->route('store-website-country-shipping.index')->with('success','Data saved successfully.');
                        }else{
                            return redirect()->route('store-website-country-shipping.index')->with('error', $response[0]->Message ?? 'Something went wrong');
                        }

                    }
                }
            }else{
                $storeWebsites = StoreWebsite::where('deleted_at',null)->get();
                $simplyDutyCountry = SimplyDutyCountry::get();
                return view('store-website-country-shipping.create',compact('storeWebsites','simplyDutyCountry'));
            }

        } catch (Exception $e) {
            return redirect()->route('store-website-country-shipping.index')->with('error', $e->getMessage() );
        }
    }

    public function edit($id = null)
    {
        $data = StoreWebsitesCountryShipping::whereId($id)->first();
        $storeWebsites = StoreWebsite::where('deleted_at',null)->get();
        $simplyDutyCountry = SimplyDutyCountry::get();
        return view('store-website-country-shipping.edit',compact('data','storeWebsites','simplyDutyCountry'));

    }

    public function delete($id = null)
    {   
        $data = StoreWebsitesCountryShipping::whereId($id)->first();
        StoreWebsitesCountryShipping::whereId($id)->delete();
        $storeWebsites = StoreWebsite::where('id',$data->store_website_id)->first();

        $url     = $storeWebsites->website.'/default/rest/all/V1/shippingcost/delete';
        $url     = 'https://stage.suvandnat.com/default/rest/all/V1/shippingcost/delete';
        $api_key = $storeWebsites->api_token;

        $pushMagentoArr = [ 'ship_id' => $data->ship_id ];
        $headers = [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json'
        ];
        $response = \App\Helpers\GuzzleHelper::post($url,$pushMagentoArr,$headers);
        return redirect()->route('store-website-country-shipping.index')->with('success','Record deleted successfully.');
    }

}
