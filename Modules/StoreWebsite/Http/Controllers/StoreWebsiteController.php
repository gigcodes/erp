<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\StoreWebsite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class StoreWebsiteController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $title = "List | Store Website";

        return view('storewebsite::index', compact('title'));
    }

    /**
     * records Page
     * @param  Request $request [description]
     * @return
     */
    public function records(Request $request)
    {
        $records = StoreWebsite::whereNull("deleted_at");

        $keyword = request("keyword");
        if (!empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where("website", "LIKE", "%$keyword%")
                    ->orWhere("title", "LIKE", "%$keyword%")
                    ->orWhere("description", "LIKE", "%$keyword%");
            });
        }

        $records = $records->get();

        return response()->json(["code" => 200, "data" => $records, "total" => count($records)]);
    }

    /**
     * records Page
     * @param  Request $request [description]
     * @return
     */
    public function save(Request $request)
    {
        $post = $request->all();

        $validator = Validator::make($post, [
            'title'   => 'required',
            'website' => 'required',
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

        $records = StoreWebsite::find($id);

        if (!$records) {
            $records = new StoreWebsite;
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
        $storeWebsite = StoreWebsite::where("id", $id)->first();

        if ($storeWebsite) {
            return response()->json(["code" => 200, "data" => $storeWebsite]);
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
        $storeWebsite = StoreWebsite::where("id", $id)->first();

        if ($storeWebsite) {
            $storeWebsite->delete();
            return response()->json(["code" => 200]);
        }

        return response()->json(["code" => 500, "error" => "Wrong site id!"]);
    }
}
