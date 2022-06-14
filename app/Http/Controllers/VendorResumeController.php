<?php

namespace App\Http\Controllers;

use App\VendorResume;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Foreach_;

class VendorResumeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $designation = [];
        $organization = [];
        $project = [];
        $dev_role = [];
        $tools = [];
        foreach($request->work_experiance as $key => $val){
            $designation[$key] = $request->input('designation'.$key);
            $organization[$key][] = $request->input('organization'.$key);
            foreach($request->input('project'.$key) as $pKey => $pval){
                $project[$key][$pKey][] = $pval;
                $dev_role[$key][$pKey][] = $request->input('dev_role'.$key);
                $tools[$key][$pKey][] = $request->input('tools'.$key);
            }
        }
        dd($designation);
        try{
            $vendorResume = new VendorResume();
            $vendorResume->vendor_id = $request->vendor_id;
            $vendorResume->name = $request->name;
            $vendorResume->email = $request->email;
            $vendorResume->mobile = $request->mobile;
            $vendorResume->career_objective = $request->career_objective;
            $vendorResume->work_experiance = serialize($request->work_experiance); //array
            $vendorResume->destination = serialize($request->destination); //array
            $vendorResume->organization = serialize($request->organization); //array
            $vendorResume->project = serialize($request->project); //array
            $vendorResume->dev_role = serialize($request->dev_role); //array
            $vendorResume->tools = serialize($request->tools); //array
            $vendorResume->soft_framework = $request->soft_framework;
            $vendorResume->soft_description = $request->soft_description;
            $vendorResume->soft_experience = $request->soft_experience;
            $vendorResume->soft_remark = $request->soft_remark;
            $vendorResume->father_name = $request->father_name;
            $vendorResume->dob = $request->dob;
            $vendorResume->gender = $request->gender;
            $vendorResume->marital_status = $request->marital_status;
            $vendorResume->langauge_know = $request->langauge_know;
            $vendorResume->hobbies = $request->hobbies;
            $vendorResume->address = $request->address;
            $vendorResume->save();
            return response()->json(["code" => 200, "data" => $vendorResume, "message" => 'Data stored successfully!!!']);
        } catch (\Exception $e) {
            return response()->json(["code" => 500, "data" => [], "message" => $e->getMessage()]);
        }
    }
    

    /**
     * Display the specified resource.
     *
     * @param  \App\VendorResume  $vendorResume
     * @return \Illuminate\Http\Response
     */
    public function show(VendorResume $vendorResume)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\VendorResume  $vendorResume
     * @return \Illuminate\Http\Response
     */
    public function edit(VendorResume $vendorResume)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\VendorResume  $vendorResume
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VendorResume $vendorResume)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VendorResume  $vendorResume
     * @return \Illuminate\Http\Response
     */
    public function destroy(VendorResume $vendorResume)
    {
        //
    }
}
