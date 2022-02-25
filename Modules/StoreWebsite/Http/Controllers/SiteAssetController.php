<?php

namespace Modules\StoreWebsite\Http\Controllers;

use Auth;
use DB;
use App\StoreWebsite;
use App\SiteDevelopment;
use App\SiteDevelopmentCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class SiteAssetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = array();
        $data['store_websites'] = StoreWebsite::select('store_websites.*')->join('site_developments','store_websites.id','=','site_developments.website_id')->where('status',">", 0)->groupBy('store_websites.id')->get();
        $data['site_development_categories'] = SiteDevelopmentCategory::select('site_development_categories.*')->join('site_developments','site_development_categories.id','=','site_developments.site_development_category_id')->where('status',">", 0)->groupBy('site_development_categories.id')->get();
        return view('storewebsite::site-asset.index', $data);
    }
    
}
