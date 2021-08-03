<?php

namespace App\Http\Controllers;

use App\MemoryUsage;
use App\Setting;
use Illuminate\Http\Request;

class MemoryUsesController extends Controller
{

    public function index()
    {
        $memoryUses = MemoryUsage::latest()->paginate(Setting::get('pagination',20));
        return view('memory',compact('memoryUses'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function updateThresoldLimit(Request $request)
    {
        
        $updatedData =    Setting::updateOrCreate([
            'name'=>'thresold_limit_for_memory_uses',
        ],[
            'val'=>$request->limit,
            'type'=>'number'
        ]);

        return response()->json(["code" => 200, "message" => 'Thresold limit updated to ' . $updatedData->val]);


    }

}
