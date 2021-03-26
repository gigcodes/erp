<?php

namespace App\Http\Controllers\gtmetrix;

use App\Http\Controllers\Controller;
use App\StoreViewsGTMetrix;
use App\WebsiteStoreView;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class WebsiteStoreViewGTMetrixController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list       = StoreViewsGTMetrix::orderBy('created_at','desc')->paginate(30);
        $cronStatus = Setting::where('name',"gtmetrixCronStatus")->get()->first();
        $cronTime   = Setting::where('name',"gtmetrixCronType")->get()->first();
        return view('gtmetrix.index',compact('list','cronStatus','cronTime'));
    }

    public function saveGTmetrixCronStatus($status = null)
    {
        
        if( empty( $status ) ) {
            return redirect()->back()->with('error','Error');
        }

        $statusExit = Setting::where('name',"gtmetrixCronStatus")->get()->first();
        if(empty($statusExit)) {
            $status_date['name'] = "gtmetrixCronStatus";
            $status_date['type'] = "string";
            $status_date['val']  = $status;
            Setting::create( $status_date );
        } else {
            $statusExit->val = $status;
            $statusExit->save();
        }
        return redirect()->back()->with('success','Success');

    }

    public function saveGTmetrixCronType(Request $request)
    {
        
        $request->validate([
            'type' => 'required'
        ]);

        $type = Setting::where('name',"gtmetrixCronType")->get()->first();

        if(empty($type)) {
           
            $type['name'] = "gtmetrixCronType";
            $type['type'] = "string";
            $type['val']  = $request->type;
            Setting::create($type);

        } else {
            
            $type->val = $request->type;
            $type->save();
            
        }
        return redirect()->back()->with('success','Success');
    }
}
