<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\StoreWebsite;
use App\StoreWebsiteUsers;

class MagentoUserFromErpController extends Controller
{
    public function index(Request $request)
    {
        $storeWebsites = StoreWebsiteUsers::select(
            'store_websites.id as store_website_id',
            'store_website_users.username', 
            'store_website_users.password', 
            'store_websites.website',
            'store_websites.title',
            'store_website_users.website_mode',
            'store_websites.store_code_id',
            'store_websites.created_at',
            'store_websites.magento_url'
        )->join('store_websites', function ($q)  {
            $q->on('store_websites.id', '=', 'store_website_users.store_website_id');
            $q->where('store_websites.website_source','=','magento');
        })
        ->where('store_website_users.is_deleted', 0)
        ->whereNull('store_websites.deleted_at')
        ->groupBy('store_website_users.username','store_websites.website','store_website_users.website_mode','store_websites.store_code_id')
        ->orderBy('store_websites.website','DESC');
        
        //Apply store_website_id if exists
        if($request->get('store_website_id')) {
            $storeWebsites->where('store_websites.id', $request->get('store_website_id'));
        }

        //Apply username if exists
        if($request->get('username')) {
            $storeWebsites->where('store_website_users.username', $request->get('username'));
        }

        //show 20 records per page
        $storeWebsites = $storeWebsites->paginate(20);

        //For select website filter list
        $allStoreWebsites = StoreWebsite::where('website_source','=','magento')
        ->whereNotNull('magento_username')
        ->whereNull('deleted_at')
        ->pluck('website','id')
        ->toArray();

        //For select role filter list 
        $magentoRoles = [];

        return view('magento-user-from-erp.index', compact('storeWebsites','magentoRoles', 'allStoreWebsites'));
    }
}
