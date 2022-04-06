<?php

namespace Modules\StoreWebsite\Http\Controllers;

use Auth;
use DB;
use App\User;
use App\StoreWebsite;
use App\SiteDevelopment;
use App\SiteDevelopmentCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Storage;

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
        $data['all_store_websites'] = StoreWebsite::all();
        $data['categories'] = SiteDevelopmentCategory::all();
        $data['search_website'] = isset($request->store_webs)? $request->store_webs : '';
        $data['search_category'] = isset($request->categories)? $request->categories : '';
        $store_websites = StoreWebsite::select('store_websites.*')->join('site_developments','store_websites.id','=','site_developments.website_id');
        if($data['search_website'] != ''){
            $store_websites =  $store_websites->where('store_websites.id', $data['search_website']);
        }
        $data['store_websites'] =  $store_websites->where('is_site_asset', 1)->groupBy('store_websites.id')->get();
        $site_development_categories = SiteDevelopmentCategory::select('site_development_categories.*')->join('site_developments','site_development_categories.id','=','site_developments.site_development_category_id')->where('is_site_asset', 1);
        if($data['search_category'] != ''){
            $site_development_categories =  $site_development_categories->where('site_development_categories.id',  $data['search_category']);
        }
        $data['site_development_categories'] = $site_development_categories->groupBy('site_development_categories.id')->get();
        $data['allUsers'] = User::select('id', 'name')->get();
        return view('storewebsite::site-asset.index', $data);
    }
	
	  
    public function siteCheckList(Request $request)
    {
        $data = array();
        $data['allStatus'] = \App\SiteDevelopmentStatus::pluck("name", "id")->toArray();
        $data['all_store_websites'] = StoreWebsite::all();
        $data['categories'] = SiteDevelopmentCategory::all();
        $data['search_website'] = isset($request->store_webs)? $request->store_webs : '';
        $data['search_category'] = isset($request->categories)? $request->categories : '';
        $store_websites = StoreWebsite::select('store_websites.*')->join('site_developments','store_websites.id','=','site_developments.website_id');
        if($data['search_website'] != ''){
            $store_websites =  $store_websites->where('store_websites.id', $data['search_website']);
        }
        $data['store_websites'] =  $store_websites->where('is_site_list', 1)->groupBy('store_websites.id')->get();
        
        $site_dev = SiteDevelopment::select(DB::raw('site_development_category_id,site_developments.id as site_development_id,website_id'));
        
        $site_development_categories = SiteDevelopmentCategory::select('site_development_categories.*', 'site_developments.site_development_master_category_id', 'site_dev.website_id', 'site_dev.site_development_id')
            ->join('site_developments','site_development_categories.id','=','site_developments.site_development_category_id')
            ->joinSub($site_dev, 'site_dev', function ($join)
            {
                $join->on('site_development_categories.id', '=', 'site_dev.site_development_category_id');
            })
            ->where('is_site_list', 1);
        if($data['search_category'] != ''){
            $site_development_categories =  $site_development_categories->where('site_development_categories.id',  $data['search_category']);
        }
      
        $data['site_development_categories'] = $site_development_categories->leftJoin('store_development_remarks', 'store_development_remarks.store_development_id', '=', 'site_developments.id')->groupBy('site_development_categories.id')->get();
        // dd($data);
        $data['allUsers'] = User::select('id', 'name')->get();
        return view('storewebsite::site-check-list.index', $data);
    }

    /**
     * Download a listing of the images.
     *
     * @return \Illuminate\Http\Response
     */
    public function downaloadSiteAssetData(Request $request)
    {
        $store_website = json_decode($request->download_website_id);
        $media_type = $request->media_type;
        $dir = public_path() . "/download_asset";
        if ( !is_dir( $dir ) ) {
            mkdir( $dir );       
        }
        $file_name = "asset_".uniqid().'.zip';
        $dir = public_path() . "/download_asset/".$file_name;
        
        $images =  \App\StoreWebsiteImage::leftJoin('media','store_website_images.media_id', '=', 'media.id')->whereIn('store_website_images.store_website_id', $store_website)->where('store_website_images.media_type', $media_type)->get();
        if(empty($images)){
            return redirect('/site-assets')->with('message', 'No Image data found');
        }else{
            $zip  = new \ZipArchive();
            $zip->open($dir, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            foreach($images as $image){
                $invoice_file = Storage::disk($image->disk)->path($image->directory .'/'. $image->filename.'.'.$image->extension);
                $zip->addFile($invoice_file, $image->filename.'.'.$image->extension);
            }
            $zip->close();
            return response()->download($dir);
        }
        

    }
    
}