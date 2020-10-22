<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Affiliates;
use Illuminate\Support\Facades\Validator;
use App\StoreWebsite;
class AffiliateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'website' => 'required|exists:store_websites,website'
        ]);
        if($validator->fails()){
            return response()->json(['status'=>'failed','message'=>'please check validation errors !','errors'=>$validator->errors()],400);
        }
        $storeweb = StoreWebsite::where('website', $request->website)->first();
        $affiliates = new Affiliates;
        $affiliates->store_website_id = $storeweb->id;
        $affiliates->first_name = isset($request->first_name)?$request->first_name:'';
        $affiliates->last_name = isset($request->last_name)?$request->last_name:'';
        $affiliates->phone = isset($request->phone)?$request->phone:'';
        $affiliates->emailaddress = isset($request->email)?$request->email:'';
        $affiliates->website_name = isset($request->website_name)?$request->website_name:'';
        $affiliates->url = isset($request->url)?$request->url:'';
        $affiliates->unique_visitors_per_month = isset($request->unique_visitors_per_month)?$request->unique_visitors_per_month:'';
        $affiliates->page_views_per_month = isset($request->page_views_per_month)?$request->page_views_per_month:'';
        $affiliates->address = isset($request->street_address)?$request->street_address:'';
        $affiliates->city = isset($request->city)?$request->city:'';
        $affiliates->postcode = isset($request->postcode)?$request->postcode:'';
        $affiliates->country = isset($request->postcode)?$request->country:'';
        $affiliates->location = isset($request->location)?$request->location:'';
        $affiliates->title = isset($request->hashtag_id)?$request->title:'';
        $affiliates->caption = isset($request->hashtag_id)?$request->caption:'';
        $affiliates->posted_at = isset($request->hashtag_id)?$request->posted_at:'';
        $affiliates->facebook = isset($request->hashtag_id)?$request->facebook:'';
        $affiliates->instagram = isset($request->hashtag_id)?$request->instagram:'';
        $affiliates->twitter = isset($request->hashtag_id)?$request->twitter:'';
        $affiliates->youtube = isset($request->hashtag_id)?$request->youtube:'';
        $affiliates->linkedin = isset($request->hashtag_id)?$request->linkedin:'';
        $affiliates->pinterest = isset($request->hashtag_id)?$request->pinterest:'';
        $affiliates->source = isset($request->hashtag_id)?$request->source:'';
        if($affiliates->save()){
            return response()->json([
                'status'=>'success',
                'message'=>'affiliate added successfully !'
            ],200);
        }
        return response()->json([
            'status'=>'failed',
            'message'=>'unable to add affilated !'
        ],500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
