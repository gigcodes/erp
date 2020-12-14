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
            
            $websiteStores = $websiteStores->where(function($q) use($request) {
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
            'name'             => 'required',
            'code'             => 'required',
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
        $records->save();

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
