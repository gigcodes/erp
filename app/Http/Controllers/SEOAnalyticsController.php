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

        if(empty($latestEntry) || Carbon::now()->diff(Carbon::parse($latestEntry->created_at))->days > 0){
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
            'data' => SEOAnalytics::orderBy('created_at','DESC')->paginate(20)
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

    public function filter(Request $request){
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $latestEntry = SEOAnalytics::orderBy('created_at','DESC')->first();
        if($start_date && $end_date){
            $start_date = Carbon::parse($start_date)->format('Y-m-d h:m:s');
            $end_date = Carbon::parse($end_date)->format('Y-m-d h:m:s');
            $data = SEOAnalytics::whereBetween('created_at', [$start_date, $end_date])->paginate(20);
        }else{
            $data = SEOAnalytics::orderBy('created_at', 'DESC')->paginate(20);
        }
        return view('seo.show-analytics', [
            'today' => $latestEntry,
            'data' => $data,
            'start_date' => Carbon::parse($start_date)->format('d-m-Y'),
            'end_date' => Carbon::parse($end_date)->format('d-m-Y')
        ]);
    }
}
