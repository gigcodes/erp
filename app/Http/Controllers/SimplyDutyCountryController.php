<?php

namespace App\Http\Controllers;

use App\SimplyDutyCountry;
use App\SimplyDutyCountryHistory;
use Illuminate\Http\Request;
use App\Setting;
use Response;
use App\SimplyDutySegment;
use Illuminate\Support\Facades\Auth;

class SimplyDutyCountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       
        if($request->code || $request->country){
           $query = SimplyDutyCountry::query();

            if(request('code') != null){
                $query->where('country_code','LIKE', "%{$request->code}%");
            }
            if(request('country') != null){
                $query->where('country_name','LIKE', "%{$request->country}%");
            }
            $countries = $query->paginate(Setting::get('pagination'));
        }else{
            $countries = SimplyDutyCountry::paginate(Setting::get('pagination'));
        }
        
         if ($request->ajax()) {
            return response()->json([
                'tbody' => view('simplyduty.country.partials.data', compact('countries'))->render(),
                'links' => (string)$countries->render()
            ], 200);
            }
        $segments = SimplyDutySegment::get(); 
        return view('simplyduty.country.index',compact('countries'),compact('segments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
  

    /**
     * Display the specified resource.
     *
     * @param  \App\SimplyDutyCountry  $simplyDutyCountry
     * @return \Illuminate\Http\Response
     */
    public function show(SimplyDutyCountry $simplyDutyCountry)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SimplyDutyCountry  $simplyDutyCountry
     * @return \Illuminate\Http\Response
     */
    public function edit(SimplyDutyCountry $simplyDutyCountry)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SimplyDutyCountry  $simplyDutyCountry
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SimplyDutyCountry $simplyDutyCountry)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SimplyDutyCountry  $simplyDutyCountry
     * @return \Illuminate\Http\Response
     */
    public function destroy(SimplyDutyCountry $simplyDutyCountry)
    {
        //
    }

    public function getCountryFromApi(){
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, "https://www.api.simplyduty.com/api/Supporting/supported-countries");

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch); 

        $countries = json_decode($output);
       
        foreach($countries as $country){
            $countryDetail = $country->Country;
            $countryCode = $country->CountryCode;
            //Country Code wtih Details
            $cat =  SimplyDutyCountry::where('country_code',$countryCode)->where('country_name',$countryDetail)->first();
            if($cat != '' && $cat != null){
                $cat->touch();
            }else{
                $category = new SimplyDutyCountry;
                $category->country_code = $countryCode;
                $category->country_name = $countryDetail;
                $category->save();
            }
        }

        return Response::json(array('success' => true)); 
    }

     /**
     * @SWG\Get(
     *   path="/duty/v1/get-countries",
     *   tags={"Duty"},
     *   summary="Get Countries",
     *   operationId="get-countries",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true, 
     *          type="string" 
     *      ),
     * )
     *
     */
    public function sendCountryJson(){
        $countries = SimplyDutyCountry::all();
        foreach($countries as $country){
            $countryArray[] = array('Country'=> $country->country_name , 'CountryCode' => $country->country_code);
        }
        return json_encode($countryArray);
    }
    public function updateduty(Request $request){
        if (!$request->ajax()) {
            return response()->json(['success' => false, 'message' => "Something went wrong!"]);
        }
        $country = SimplyDutyCountry::find($request->input('id'));
        $data=[
            'simply_duty_countries_id'=>$country->id,
            'old_segment'=>$country->segment_id, 
            'new_segment'=>$country->segment_id,
            'old_duty'=>$country->default_duty,
            'new_duty'=>$request->input('duty'),
            'updated_by'=>Auth::user()->id
    
        ];
        $country->default_duty = $request->input('duty');
        $country->status=0;
        SimplyDutyCountryHistory::insert($data);
        if ($country->save()) {
            $this->update_store_website_product_prices($country->country_code,$request->input('duty'));
            return response()->json(['success' => true, 'message' => "Default duty update successfully"]);
        }
        return response()->json(['success' => false, 'message' => "Something went wrong!"]);
    }

   public function addsegment(Request $request) 
   {
       $cid=$request->cid;
       $sid=$request->sid;
       $duty=SimplyDutyCountry::where('id',$cid)->first();
       $data=[
        'simply_duty_countries_id'=>$duty->id,
        'old_segment'=>$duty->segment_id, 
        'new_segment'=>$sid,
        'old_duty'=>$duty->default_duty,
        'new_duty'=>$duty->default_duty,
        'updated_by'=>Auth::user()->id  
       ];
       $duty->segment_id=$sid;
       $duty->status=0;
       $duty->save();
       SimplyDutyCountryHistory::insert($data);
       return response()->json(['success' => true, 'message' => "Segment Updated Successfully"]);

   }

   public function assignDefaultValue(Request $request)
   {
       $value=$request->value;
       $segment=$request->segment;
       if ($value>0 && $segment>0 )
       {
           //SimplyDutyCountry::where('segment_id',  $segment)->update(['default_duty'=>$value,'status'=>0]);
           $duty=SimplyDutyCountry::where('segment_id',$segment)->get();
           foreach($duty as $d)
             {
                $data=[
                    'simply_duty_countries_id'=>$d->id,
                    'old_segment'=>$d->segment_id, 
                    'new_segment'=>$d->segment_id,
                    'old_duty'=>$d->default_duty,
                    'new_duty'=>$value,
                    'updated_by'=>Auth::user()->id
            
                ];
                $d->default_duty=$value;
                $d->status=0;
                $d->save();
                SimplyDutyCountryHistory::insert($data);
                $this->update_store_website_product_prices($d->country_code,$value);
            }   
           return response()->json(["code" => 200 , "message" => "Default Duty assigned"]);
       }
       else
       {
          return response()->json(["code" => 100 , "message" => "somethings wrong"]);
       }    
   }

   public function approve(Request $request)
   {
        $ids=$request->ids;
        $ids=explode(",", $ids);
        for($i=0;$i<count($ids);$i++)
        {
            if ($ids[$i]>0)
            \App\StoreWebsiteProductPrice::where('id',$ids[$i])->update(['status'=> 1]);
               
        }
        return response()->json(["code" => 200 , "message" => "Approved Successfully"]);
   }

   public function update_store_website_product_prices($code,$amount)
   {
           $p= \App\StoreWebsiteProductPrice::select('store_website_product_prices.id','store_website_product_prices.duty_price')
           ->join('websites','store_website_product_prices.store_website_id','websites.id' )
           ->where('websites.countries',$code)
           ->first();
           if ($p)
           {
               \App\StoreWebsiteProductPrice::where('id',$p->id)->update(['duty_price'=>$amount,'status'=>0]) ;
               $note="Country Duty change  from ".$p->duty_price." To ".$amount;
               \App\StoreWebsiteProductPriceHistory::insert(['sw_product_prices_id'=>$p->id,'updated_by'=>Auth::id(),'notes'=>$note]);
           }
   }

   

}
