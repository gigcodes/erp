<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SEO\Mozscape;
use App\SEOAnalytics;
use Carbon\Carbon;

class SEOAnalyticsController extends Controller
{

    protected $url;

    public function __construct()
    {
        $this->middleware('auth');
        $this->url = env('APP_URL_FOR_SEO', 'google.com');
    }

    public function show(){
        $latestEntry = SEOAnalytics::orderBy('created_at','DESC')->first();
        if(!$latestEntry || Carbon::today()->diff(Carbon::parse($latestEntry->created_at))->days > 0){
                $data = (object) Mozscape::getSiteDetails($this->url);
                $latestEntry = new SEOAnalytics();
                $latestEntry->domain_authority = $data->domain_authority;
                $latestEntry->linking_authority = $data->linking_authority;
                $latestEntry->inbound_links = $data->inbound_links;
                $latestEntry->ranking_keywords = $data->ranking_keywords ? $data->ranking_keywords : null;
                $latestEntry->save();
        }

        return view('seo.show-analytics', [
            'today' => $latestEntry,
            'data' => SEOAnalytics::paginate(20)
        ]);
    }

    public function delete($id){
        $entry = SEOAnalytics::find($id);
        if(!$entry){
            return response()->json(['message' => 'The entry has already been removed or is inaccessible to you!'],400);
        }else{
            $entry->delete();
            return response()->json(['message' => 'The entry has been removed!'],200);
        }
    }
}
