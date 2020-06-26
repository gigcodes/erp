<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\PriceOverride;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class PriceOverrideController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $title = "Price override | Store Website";
        return view('storewebsite::price-override.index', compact('title'));
    }

    public function records(Request $request)
    {
        $modal = PriceOverride::leftJoin("brands as b", "b.id", "price_overrides.brand_id")
            ->leftJoin("categories as c", "c.id", "price_overrides.category_id")
            ->leftJoin("simply_duty_countries as sc", "sc.country_code", "price_overrides.country_code");

        if (!empty($request->keyword)) {
            $modal = $modal->where(function($q) use($request) {
                $q->orWhere("c.title","like","%".$request->keyword."%")->orWhere("b.name","like","%".$request->keyword."%")
                ->orWhere("sc.country_name","like","%".$request->keyword."%");
            });
        }

        $modal = $modal->select(["price_overrides.*", "b.name as brand_name", "c.title as category_name", "sc.country_name as country_name"]);
        $modal = $modal->orderby("price_overrides.id", "DESC")->paginate(12);

        return response()->json([
            "code"       => 200,
            "data"       => $modal->items(),
            "pagination" => (string) $modal->links(),
            "total"      => $modal->total(),
            "page"       => $modal->currentPage(),
        ]);
    }

    public function save(Request $request)
    {
        $post = $request->all();

        $validator = Validator::make($post, [
            'value'     => 'required'
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

        $records = PriceOverride::find($id);

        if (!$records) {
            $records = new PriceOverride;
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
        $po = PriceOverride::where("id", $id)->first();

        if ($po) {
            return response()->json(["code" => 200, "data" => $po]);
        }

        return response()->json(["code" => 500, "error" => "Wrong id!"]);
    }

    /**
     * delete Page
     * @param  Request $request [description]
     * @return
     */

    public function delete(Request $request, $id)
    {
        $po = PriceOverride::where("id", $id)->first();

        if ($po) {
            $po->delete();
            return response()->json(["code" => 200]);
        }

        return response()->json(["code" => 500, "error" => "Wrong id!"]);
    }
}
