<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\Http\Controllers\Controller;
use App\StoreWebsite;
use App\Website;
use App\WebsiteStore;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class WebsiteStoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = "Website Store | Store Website";

        $websites = Website::all()->pluck("name", "id");

        return view('storewebsite::website-store.index', [
            'title'    => $title,
            'websites' => $websites,
        ]);
    }

    public function records(Request $request)
    {
        $websiteStores = WebsiteStore::leftJoin('websites as w', 'w.id', 'website_stores.website_id');

        // Check for keyword search
        if ($request->keyword != null) {

            $websiteStores = $websiteStores->where(function ($q) use ($request) {
                $q->where("website_stores.name", "like", "%" . $request->keyword . "%")
                    ->orWhere("website_stores.code", "like", "%" . $request->keyword . "%");
            });
        }

        $websiteStores = $websiteStores->select(["website_stores.*", "w.name as website_name"])->paginate();

        return response()->json(["code" => 200, "data" => $websiteStores->items(), "total" => $websiteStores->total()]);
    }

    public function store(Request $request)
    {
        $post      = $request->all();
        $validator = Validator::make($post, [
            'name'       => 'required',
            'code'       => 'required',
            'website_id' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = "";
            $messages     = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : " . $er . "<br>";
                }
            }
            return response()->json(["code" => 500, "error" => $outputString]);
        }

        $id = $request->get("id", 0);

        $records = WebsiteStore::find($id);

        if (!$records) {
            $records = new WebsiteStore;
        }

        $records->fill($post);
        // if records has been save then call a request to push
        if ($records->save()) {

            // check that store store has the platform id exist
            if ($records->website && $records->website->platform_id > 0) {

                $id = \seo2websites\MagentoHelper\MagentoHelper::pushWebsiteStore([
                    "type"       => "store",
                    "name"       => $records->name,
                    "code"       => $records->code,
                    "website_id" => $records->website->platform_id,
                ], $records->website->storeWebsite);

                if (!empty($id) && is_numeric($id)) {
                    $records->platform_id = $id;
                    $records->save();
                }else{
                   return response()->json(["code" => 200, "data" => $records , "error" => "Website-Store stored but store push failed"]);
                }
            }

        }

        return response()->json(["code" => 200, "data" => $records]);
    }

    /**
     * Edit Page
     * @param  Request $request [description]
     * @return
     */

    public function edit(Request $request, $id)
    {
        $websiteStore = WebsiteStore::where("id", $id)->first();

        if ($websiteStore) {
            return response()->json(["code" => 200, "data" => $websiteStore]);
        }

        return response()->json(["code" => 500, "error" => "Wrong site id!"]);
    }

    /**
     * delete Page
     * @param  Request $request [description]
     * @return
     */

    public function delete(Request $request, $id)
    {
        $websiteStore = WebsiteStore::where("id", $id)->first();

        if ($websiteStore) {
            $websiteStore->delete();
            return response()->json(["code" => 200]);
        }

        return response()->json(["code" => 500, "error" => "Wrong site id!"]);
    }
}
