<?php

namespace App\Http\Controllers;

use App\Influencers;
use App\InfluencersDM;
use App\InfluencerKeyword;
use Illuminate\Http\Request;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class InfluencersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *
     * List all influencers
     */
    public function index()
    {
        $hashtags = Influencers::all();
        $keywords = InfluencerKeyword::all();
        return view('instagram.influencers.index', compact('hashtags'));
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
     * CReate a new influencer record..
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $i = new Influencers();
        $i->username = $request->get('name');
        $i->brand_name = $request->get('brand_name');
        $i->blogger = $request->get('blogger');
        $i->city = $request->get('city');
        $i->save();

        return redirect()->back()->with('message', 'Added instagram influencer.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Influencers  $influencers
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $comments = InfluencersDM::all();

        return view('instagram.influencers.comments', compact('comments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Influencers  $influencers
     * @return \Illuminate\Http\Response
     */
    public function edit(Influencers $influencers)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Influencers  $influencers
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Influencers $influencers)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Influencers  $influencers
     * @return \Illuminate\Http\Response
     */
    public function destroy(Influencers $influencers)
    {
        //
    }

    public function saveKeyword(Request $request)
    {
        $name = $request->name;
        
        $keywordCheck = InfluencerKeyword::where('name',$name)->first();
        
        if(!$keywordCheck){
            $keyword = new InfluencerKeyword();
            $keyword->name = $name;
            $keyword->instagram_account_id = $request->get('instagram_account_id',null);
            $keyword->save();
            return response()->json(['message' => 'Influencer Keyword Saved']); 
        }else{
            $keywordCheck->name = $name;
            $keywordCheck->instagram_account_id = $request->get('instagram_account_id',null);
            $keywordCheck->save();
            return response()->json(['message' => 'Influencer Keyword Saved']); 
        }
        
        return response()->json(['message' => 'Influencer Keyword Exist']);
        
    }

    public function getScraperImage(Request $request)
    {
     $name = $request->name;

     $name = str_replace(" ","",$name);

     $cURLConnection = curl_init();

     $url = env('INFLUENCER_SCRIPT_URL').':'.env('INFLUENCER_SCRIPT_PORT').'/get-image?'.$name;

     //echo $url;
     //die();
     curl_setopt($cURLConnection, CURLOPT_URL, $url);
     
     curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

     $phoneList = curl_exec($cURLConnection);
     curl_close($cURLConnection);

     $jsonArrayResponse = json_decode($phoneList);

     $b64 = $jsonArrayResponse->status;

     if($jsonArrayResponse->status == 'Something Went Wrong'){
        return \Response::json(array('success' => false,'message' => 'No Image Available')); 
    } 
    $content = base64_decode($b64);

    $media = MediaUploader::fromString($content)->toDirectory('/influencer')->useFilename($name)->upload();
    
    return \Response::json(array('success' => true,'message' => $media->getUrl()));
    }

    public function checkScraper(Request $request)
    {
       $name = $request->name;

       $name = str_replace(" ","",$name);

       $cURLConnection = curl_init();
        $url = env('INFLUENCER_SCRIPT_URL').':'.env('INFLUENCER_SCRIPT_PORT').'/get-status?'.$name;
        
        curl_setopt($cURLConnection, CURLOPT_URL, $url);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

        $phoneList = curl_exec($cURLConnection);
        curl_close($cURLConnection);

        $jsonArrayResponse = json_decode($phoneList);

        $b64 = $jsonArrayResponse->status;

        return \Response::json(array('success' => true,'message' => $b64));
       
    }

    public function startScraper(Request $request)
    {
       $name = $request->name;

       $name = str_replace(" ","",$name);

       $cURLConnection = curl_init();

        $url = env('INFLUENCER_SCRIPT_URL').':'.env('INFLUENCER_SCRIPT_PORT').'/start-script?'.$name;
        
       // $url = str_replace('\u2029','',$url);


        //echo $url;
        //die();

        curl_setopt($cURLConnection, CURLOPT_URL, $url);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

        $phoneList = curl_exec($cURLConnection);
        curl_close($cURLConnection);

        $jsonArrayResponse = json_decode($phoneList);

        $b64 = $jsonArrayResponse->status;

        return \Response::json(array('success' => true,'message' => $b64));
       
    }

    public function getLogFile(Request $request)
    {
        $name = $request->name;

        $name = str_replace(" ","",$name);

        $cURLConnection = curl_init();

        $url = env('INFLUENCER_SCRIPT_URL').':'.env('INFLUENCER_SCRIPT_PORT').'/send-log?'.$name;
        // echo $url;
        // die();
        curl_setopt($cURLConnection, CURLOPT_URL, $url);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

        $phoneList = curl_exec($cURLConnection);
        curl_close($cURLConnection);

        $jsonArrayResponse = json_decode($phoneList);
        
        $b64 = $jsonArrayResponse->status;
        
        if($jsonArrayResponse->status == 'Something Went Wrong'){
            return \Response::json(array('success' => false,'message' => 'No Logs Available')); 
        } 
        $content = base64_decode($b64);

        $media = MediaUploader::fromString($content)->toDirectory('/influencer')->useFilename($name)->upload();
    
        return \Response::json(array('success' => true,'message' => $media->getUrl()));
       
    }

    public function restartScript(Request $request)
    {
       $name = $request->name;

       $name = str_replace(" ","",$name);

       $cURLConnection = curl_init();

        $url = env('INFLUENCER_SCRIPT_URL').':'.env('INFLUENCER_SCRIPT_PORT').'/restart-script?'.$name;

        // echo $url;
        // die();

        curl_setopt($cURLConnection, CURLOPT_URL, $url);

        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

        $phoneList = curl_exec($cURLConnection);
        curl_close($cURLConnection);

        $jsonArrayResponse = json_decode($phoneList);

        $b64 = $jsonArrayResponse->status;

        return \Response::json(array('success' => true,'message' => $b64));
       
    }

    public function stopScript(Request $request)
    {
       $name = $request->name;

       $name = str_replace(" ","",$name);

       $cURLConnection = curl_init();
        $url = env('INFLUENCER_SCRIPT_URL').':'.env('INFLUENCER_SCRIPT_PORT').'/stop-script?'.$name;
        // echo $url;
        // die();
        curl_setopt($cURLConnection, CURLOPT_URL, $url);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

        $phoneList = curl_exec($cURLConnection);
        curl_close($cURLConnection);

        $jsonArrayResponse = json_decode($phoneList);

        $b64 = $jsonArrayResponse->status;

        return \Response::json(array('success' => true,'message' => $b64));
       
    }

    public function sortData()
    {
        try {
           
           \Artisan::call('influencer:description'); 

            return response()->json('Console Commnad Ran',200);   
        
        } catch (\Exception $e) {
            
            return response()->json('Cannot call artisan command',200); 
        } 
        
    }


}
