<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\Http\Controllers\Controller;
use App\StoreWebsite;
use App\Website;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class WebsiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = "Website | Store Website";

        $storeWebsites = StoreWebsite::all()->pluck("website", "id");

        return view('storewebsite::website.index', [
            'title'         => $title,
            'storeWebsites' => $storeWebsites,
        ]);
    }

    public function records(Request $request)
    {
        $websites = Website::leftJoin('store_websites as sw', 'sw.id', 'websites.store_website_id');

        // Check for keyword search
        if ($request->keyword != null) {
            $websites = $websites->where(function ($q) use ($request) {
                $q->where("websites.name", "like", "%" . $request->keyword . "%")
                    ->orWhere("websites.code", "like", "%" . $request->keyword . "%");
            });
        }

        $websites = $websites->select(["websites.*", "sw.website as store_website_name"])->paginate();

        return response()->json(["code" => 200, "data" => $websites->items(), "total" => $websites->total()]);
    }

    public function store(Request $request)
    {
        $post      = $request->all();
        $validator = Validator::make($post, [
            'name'             => 'required',
            'code'             => 'required',
            'store_website_id' => 'required',
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

        $records = Website::find($id);

        if (!$records) {
            $records = new Website;
        }

        $records->fill($post);

        // if records has been save then call a request to push
        if ($records->save()) {
            $id = \seo2websites\MagentoHelper\MagentoHelper::pushWebsite([
                "type" => "website",
                "name" => $records->name,
                "code" => $records->code,
            ],$records->storeWebsite);

            if(!empty($id) && is_numeric($id)) {
               $records->platform_id = $id; 
               $records->save();
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
        $website = Website::where("id", $id)->first();

        if ($website) {
            return response()->json(["code" => 200, "data" => $website]);
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
        $website = Website::where("id", $id)->first();

        if ($website) {
            $website->delete();
            return response()->json(["code" => 200]);
        }

        return response()->json(["code" => 500, "error" => "Wrong site id!"]);
    }
}
