<?php

namespace App\Http\Controllers;

use App\GoogleAdsAccount;
use App\GoogleAdsCampaign;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdsController extends Controller
{
    public function index(Request $request)
    {
        $title  = "Ads";
        
        return view("ads.index", compact('title'));
    }

    public function records(Request $request)
    {
        $records = GoogleAdsAccount::leftJoin("googlecampaigns as gc","gc.account_id","googleadsaccounts.id")
        ->leftJoin("googleadsgroups as gg","gg.adgroup_google_campaign_id","gc.google_campaign_id")
        ->leftJoin("googleads as ga","ga.google_adgroup_id","gg.google_adgroup_id");

        $keyword = request("keyword");
        if (!empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where("gg.ad_group_name", "LIKE", "%$keyword%")
                ->orWhere("gc.campaign_name", "LIKE", "%$keyword%")
                ->orWhere("ga.headline1", "LIKE", "%$keyword%");
            });
        }

        $records = $records->get();

        return response()->json(["code" => 200, "data" => $records, "total" => count($records)]);
    }

}
