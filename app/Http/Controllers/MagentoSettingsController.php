<?php

namespace App\Http\Controllers;

use App\MagentoSetting;
use App\StoreWebsite;
use App\WebsiteStore;
use App\WebsiteStoreView;
use Illuminate\Http\Request;


class MagentoSettingsController extends Controller
{

    public function index(Request $request)
    {

        $magentoSettings = MagentoSetting::with(
            'storeview.websiteStore.website.storeWebsite', 
            'store.website.storeWebsite')
            ->orderBy('created_at', 'DESC')
            ->paginate(100);

      //  dd($magentoSettings);

        return view('magento.settings.index', [
            'magentoSettings' => $magentoSettings
        ]);
    }

    public function create(Request $request)
    {

        foreach ($request->scope as $scope) {

            if ($scope === 'default') {

                $storeWebsites = StoreWebsite::get();

                foreach($storeWebsites as $storeWebsite){

                    MagentoSetting::create([
                        'scope' => $scope,
                        'scope_id' => $storeWebsite->id,
                        'name' => $request->name,
                        'path' => $request->path,
                        'value' => $request->value
                    ]);

                }
            }

            if($scope === 'websites'){

                $websiteStores = WebsiteStore::get();

                foreach($websiteStores as $websiteStore){

                    MagentoSetting::create([
                        'scope' => $scope,
                        'scope_id' => $websiteStore->id,
                        'name' => $request->name,
                        'path' => $request->path,
                        'value' => $request->value
                    ]);

                }
                
            }

            if($scope === 'stores'){

                $websiteStoresViews = WebsiteStoreView::get();

                foreach($websiteStoresViews as $websiteStoresView){

                    MagentoSetting::create([
                        'scope' => $scope,
                        'scope_id' => $websiteStoresView->id,
                        'name' => $request->name,
                        'path' => $request->path,
                        'value' => $request->value
                    ]);

                }
                
            }

        }

        return response()->json(['status' => true]);

    }

    public function update(Request $request)
    {
    }
}
