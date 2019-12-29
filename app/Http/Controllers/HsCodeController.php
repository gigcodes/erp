<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\HsCode;
use App\Setting;
use App\HsCodeSetting;
use Redirect;

class HsCodeController extends Controller
{
    public function index(Request $request){
    	if($request->code || $request->description){
           $query = HsCode::query();

            if(request('code') != null){
                $query->where('code', request('code'));
            }
            if(request('description') != null){
                $query->where('description','LIKE', "%{$request->description}%");
            }
            $categories = $query->paginate(Setting::get('pagination'));
        }else{
            $categories = HsCode::paginate(Setting::get('pagination'));
        }
        
         if ($request->ajax()) {
            return response()->json([
                'tbody' => view('simplyduty.hscode.partials.data', compact('categories'))->render(),
                'links' => (string)$categories->render()
            ], 200);
            }

        return view('simplyduty.hscode.index',compact('categories'));
    }

    public function saveKey(Request $request)
    {
        $setting = HsCodeSetting::all();
        if(count($setting) == 0){
            $set = new HsCodeSetting();
            $set->key = $request->key;
            $set->save();
        }else{
            $set = HsCodeSetting::find(1);
            $set->key = $request->key;
            $set->save();
        }

        return Redirect::to('/product/hscode');

    }
}
