<?php

namespace App\Http\Controllers;

use App\GoogleScrapperKeyword;
use Illuminate\Http\Request;
use DB;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class GoogleScrapperController extends Controller
{
    public function index()
    {
        // $hashtags = Influencers::all();
        $keywords = GoogleScrapperKeyword::all();
        return view('google-scrapper.index', compact('keywords') );
    }


    public function saveKeyword(Request $request)
    {

        $keywordData               = new GoogleScrapperKeyword();
        $keywordData->keyword      = $request->get('name');
        $keywordData->start       = $request->get('start');
        $keywordData->end = $request->get('end');
        $keywordData->save();
        return response()->json(['message' => 'Google Scrapper Keyword Saved']); 
        
    }

    public function extractedData(Request $request)
    {
        var_dump($request);
    }
    
}
