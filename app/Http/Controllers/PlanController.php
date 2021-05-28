<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StoreWebsiteAnalytic;
use App\StoreWebsite;
use App\Plan;
use App\PlanBasisStatus;
use App\PlanTypes;
use Illuminate\Support\Facades\Validator;
use Storage;
use File;

class PlanController extends Controller
{

    public function __construct()
    {

    }

    public function index()
    {
        $query = Plan::whereNull('parent_id');
        $basisList = PlanBasisStatus::all();
        $typeList = PlanTypes::all();

        if(request('status')){
            $query->where('status',request('status'));
        }

        if(request('priority')){
            $query->where('priority',request('priority'));
        }
        if(request('typefilter')){
            $query->where('type',request('typefilter'));
        }

        if(request('date')){
            $query->whereDate('date',request('date'));
        }

        if(request('term')){
            $query->where('subject', 'LIKE', '%' . request('term') . '%');
            $query->orwhere('sub_subject', 'LIKE', '%' . request('term') . '%');
            $query->orwhere('basis', 'LIKE', '%' . request('term') . '%');
            $query->orwhere('implications', 'LIKE', '%' . request('term') . '%');
        }

        $planList = $query->orderBy('id','DESC')->paginate(10);
        return view('plan-page.index', compact('planList','basisList','typeList'));
    }

    public function store(Request $request)
    {   
        //dd( $request->all() );
            $rules = [
                'priority' => 'required',
                'date' => 'required',
                'status' => 'required',
            ];

            $validation = validator(
               $request->all(),
               $rules
            );
            //If validation fail send back the Input with errors
            if($validation->fails()) {
                //withInput keep the users info
                return redirect()->back()->withErrors($validation)->withInput();
            } else {
                $data = array(
                    'subject' => $request->subject,
                    'sub_subject' => $request->sub_subject,
                    'description' => $request->description,
                    'priority' => $request->priority,
                    'date' => $request->date,
                    'status' => $request->status,
                    'budget' => $request->budget,
                    'deadline' => $request->deadline,
                    'basis' => $request->basis,
                    'type' => $request->type,
                    'implications' => $request->implications,
                );
                if( $request->parent_id ){
                    $data['parent_id'] = $request->parent_id;
                }
                if( $request->remark ){
                    $data['remark'] = $request->remark;
                }
                if($request->id){
                    Plan::whereId($request->id)->update($data);
                    return redirect()->back()->with('success','Plan updated successfully.');
                }else{
                    Plan::insert($data);
                    return redirect()->back()->with('success','Plan saved successfully.');
                }
            }
        
    }

    public function newBasis(Request $request)
    {
        $rules = [
            'name' => 'required',
        ];

        $validation = validator(
           $request->all(),
           $rules
        );
        if($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $data = array(
            'status' => $request->name,
        );

        PlanBasisStatus::insert($data);

        return redirect()->back()->with('success','New status created successfully.');

    }
    public function newType(Request $request)
    {
        $rules = [
            'name' => 'required',
        ];

        $validation = validator(
           $request->all(),
           $rules
        );
        if($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $data = array(
            'type' => $request->name,
        );

        PlanTypes::insert($data);

        return redirect()->back()->with('success','New type created successfully.');

    }

    public function edit(Request $request)
    {
        $data = Plan::where('id' ,$request->id)->first();
        if($data){
            return response()->json([
                "code" => 200,
                "object" => $data,
            ]);

        }
        return response()->json([
                "code" => 500,
                "object" => null,
         ]);

    }

    public function delete($id = null)
    {
        StoreWebsiteAnalytic::whereId($id)->delete();
        return redirect()->to('/store-website-analytics/index')->with('success','Record deleted successfully.');
    }

    public function report($id = null) 
    {
        $reports = \App\ErpLog::where('model',\App\StoreWebsiteAnalytic::class)->orderBy("id","desc")->where("model_id",$id)->get();
        return view("store-website-analytics.reports",compact('reports'));
    }

}
