<?php

namespace App\Http\Controllers;

use App\GoogleScrapper;
use App\Influencers;
use App\InfluencersDM;
use App\InfluencerKeyword;
use App\InfluencersHistory;
use Illuminate\Http\Request;
use DB;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class GoogleScrapperController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *
     * List all GoogleScrapper
     */
    public function index()
    {
        // $hashtags = Influencers::all();
        // $keywords = InfluencerKeyword::all();
        return view('google-scrapper.partials.google-scrapper-data');
    }

}
