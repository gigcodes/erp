<?php

namespace App\Http\Controllers;

use App\Uicheck;
/*use Illuminate\Http\Request;
use App\SiteDevelopment;
use App\SiteDevelopmentArtowrkHistory;
use App\SiteDevelopmentCategory;
use App\SiteDevelopmentMasterCategory;
use App\StoreWebsite;
use DB;
*/
use Auth;
use DB;
use App\User;
use App\StoreWebsite;
use App\SiteDevelopmentDocument;
use App\SiteDevelopment;
use App\SiteDevelopmentStatus;
use App\SiteDevelopmentCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Storage;
use PDF;

class UicheckController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id = null, Request $request)
    {
        $data = array();
        $data['all_store_websites'] = StoreWebsite::all();
        $data['categories'] = SiteDevelopmentCategory::paginate(20);//all();
        $data['search_website'] = isset($request->store_webs)? $request->store_webs : '';
        $data['search_category'] = isset($request->categories)? $request->categories : '';
        $data['site_development_status_id'] = isset($request->site_development_status_id)? $request->site_development_status_id : [];
        $data['allStatus'] = SiteDevelopmentStatus::pluck("name", "id")->toArray();
        $store_websites = StoreWebsite::select('store_websites.*')->join('site_developments','store_websites.id','=','site_developments.website_id');
        if($data['search_website'] != ''){
            $store_websites =  $store_websites->where('store_websites.id', $data['search_website']);
        }
        $data['store_websites'] =  $store_websites->where('is_site_asset', 1)->groupBy('store_websites.id')->get();
        $site_development_categories = SiteDevelopmentCategory::select('site_development_categories.*', 'site_developments.id AS site_id')
            ->join('site_developments','site_development_categories.id','=','site_developments.site_development_category_id')
            ->where('is_ui', 1);

        if($data['search_category'] != ''){
            $site_development_categories =  $site_development_categories->where('site_development_categories.id',  $data['search_category']);
        }
        
        if(isset($request->site_development_status_id) && !empty($request->site_development_status_id)){
            $site_development_categories =  $site_development_categories->where('site_developments.status',  $data['site_development_status_id']);
        }
        $data['site_development_categories'] = $site_development_categories->groupBy('site_development_categories.id')->get();
        $data['allUsers'] = User::select('id', 'name')->get();
        $data['log_user_id'] = \Auth::user()->id ?? '';
        return view('uicheck.index', $data );
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
        try{
            $uicheck = Uicheck::find($request->id);
            if(empty($uicheck))
                $uicheck = new Uicheck();

            $uicheck->site_development_id = $request->site_development_id;
            $uicheck->site_development_category_id = $request->category;

            if($request->website_id)
                $uicheck->website_id = $request->website_id;
            if($request->issue)
                $uicheck->issue = $request->issue;
            if($request->developer_status)
                $uicheck->dev_status_id = $request->developer_status;
            if($request->admin_status)
                $uicheck->admin_status_id = $request->admin_status;
                

            $uicheck->save();
            return response()->json(['code' => 200, 'data' => $uicheck,'message' => 'Updated successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Uicheck  $uicheck
     * @return \Illuminate\Http\Response
     */
    public function show(Uicheck $uicheck)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Uicheck  $uicheck
     * @return \Illuminate\Http\Response
     */
    public function edit(Uicheck $uicheck)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Uicheck  $uicheck
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Uicheck $uicheck)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Uicheck  $uicheck
     * @return \Illuminate\Http\Response
     */
    public function destroy(Uicheck $uicheck)
    {
        //
    }
}
