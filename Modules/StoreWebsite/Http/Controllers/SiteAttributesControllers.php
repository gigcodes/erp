<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\ChatMessage;
use App\Http\Controllers\WhatsAppController;
use App\StoreWebsiteAttributes;
use Auth;
use Crypt;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use App\SocialStrategySubject;
use App\Setting;
use App\User;
use App\SocialStrategy;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class SiteAttributesControllers extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $title = "List | Site Attributes";

        return view('storewebsite::site-attributes.index', compact('title'));
    }

    /**
     * Store Page
     * @param  Request $request [description]
     * @return
     */
    public function store(Request $request)
    {
        $post = $request->all();
        $validator = Validator::make($post, [
            'attribute_key'       => 'required',
            'attribute_val'       => 'required',
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
        $records = StoreWebsiteAttributes::where('attribute_key', $request->attribute_key)->where('store_website_id',$request->store_website_id)->first();

        if (!$records) {
            $records = new StoreWebsiteAttributes;
        }
        $records->fill($post);
        // if records has been save then call a request to push
        if ($records->save()) {

        }
        
        return response()->json(["code" => 200, "data" => $records]);
    }

    /**
     * Index Page
     * @param  Request $request [description]
     * @return
     */
    public function records(Request $request)
    {
        $StoreWebsiteAttributesViews = StoreWebsiteAttributes::all();

        return response()->json(["code" => 200, "data" => $StoreWebsiteAttributesViews]);
    }


    /**
     * delete Page
     * @param  Request $request [description]
     * @return
     */

    public function delete(Request $request, $id)
    {
        $StoreWebsiteAttributes = StoreWebsiteAttributes::where("id", $id)->first();

        if ($StoreWebsiteAttributes) {
            $StoreWebsiteAttributes->delete();
            return response()->json(["code" => 200]);
        }

        return response()->json(["code" => 500, "error" => "Wrong attribute id!"]);
    }

    
    /**
     * Edit Page
     * @param  Request $request [description]
     * @return
     */

    public function edit(Request $request, $id)
    {
        $StoreWebsiteAttributes = StoreWebsiteAttributes::where("id", $id)->first();

        if ($StoreWebsiteAttributes) {
            return response()->json(["code" => 200, "data" => $StoreWebsiteAttributes]);
        }

        return response()->json(["code" => 500, "error" => "Wrong site id!"]);
    }
   
}
