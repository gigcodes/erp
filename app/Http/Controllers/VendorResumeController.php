<?php

namespace App\Http\Controllers;

use App\VendorResume;
use App\Setting;
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
        $resumes = VendorResume::latest()->paginate(Setting::get('pagination'));
        return view('vendors.list-cv', [
            'resumes' => $resumes,
        ]);
    }

    public function search(Request $request) {

        $resumes = VendorResume::latest();
        //if vendoe_id is not null
        if ($request->vendoe_id != null) {
            $resumes->where('vendoe_id', $request->vendoe_id);
        }

        //If first_name is not null
        if ($request->first_name != null) {
            $resumes->where('first_name', 'LIKE', '%' . $request->first_name . '%');
        }

        //if second_name is not null
        if ($request->second_name != null) {
            $resumes->where('second_name', 'LIKE', '%' . $request->second_name . '%');
        }

        //if email is not null
        if ($request->email != null) {
            $resumes->where('email', 'LIKE', '%' . $request->email . '%');
        }

        //if mobile is not null
        if ($request->mobile != null) {
            $resumes->where('mobile', 'LIKE', '%' . $request->mobile . '%');
        }

        //if expected_salary_in_usd is not null
        if ($request->expected_salary_in_usd != null) {
            $resumes->where('expected_salary_in_usd', 'LIKE', '%' . $request->expected_salary_in_usd . '%');
        }

        $resumes = $resumes->paginate(Setting::get('pagination'));
        return view('vendors.list-cv', [
            'resumes' => $resumes,
        ]);

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
        $project = [];
        $dev_role = [];
        $tools = [];
        foreach($request->work_experiance as $key => $val){
            foreach($request->input('project'.$key) as $pKey => $pval){
                $project[$key][$pKey] = $pval;
                $dev_role[$key][$pKey] = $request->input('dev_role'.$key);
                $tools[$key][$pKey] = $request->input('tools'.$key);
            }
        }
        //dd($project);
        try{
            $vendorResume = new VendorResume();
            $vendorResume->vendor_id = $request->vendor_id;
            $vendorResume->pre_name = $request->pre_name;
            $vendorResume->first_name = $request->first_name;
            $vendorResume->second_name = $request->second_name;
            $vendorResume->email = $request->email;
            $vendorResume->mobile = $request->mobile;
            $vendorResume->career_objective = $request->career_objective;
            $vendorResume->salary_in_usd = $request->salary_in_usd;
            $vendorResume->expected_salary_in_usd = $request->expected_salary_in_usd;
            $vendorResume->preferred_working_hours = $request->preferred_working_hours;
            $vendorResume->start_time = $request->start_time;
            $vendorResume->end_time = $request->end_time;
            $vendorResume->time_zone = $request->time_zone;
            $vendorResume->preferred_working_days = $request->preferred_working_days;
            $vendorResume->start_day = $request->start_day;
            $vendorResume->end_day = $request->end_day;
            $vendorResume->full_time = $request->full_time;
            $vendorResume->part_time = $request->part_time;
            $vendorResume->job_responsibilities = $request->job_responsibilities;
            $vendorResume->projects_worked = $request->projects_worked;
            $vendorResume->fulltime_freelancer = serialize($request->fulltime_freelancer);
            $vendorResume->current_assignments = serialize($request->current_assignments);
            $vendorResume->current_assignments_description = serialize($request->current_assignments_description);
            $vendorResume->current_assignments_hours_utilisted = serialize($request->current_assignments_hours_utilisted);
            $vendorResume->work_experiance = serialize($request->work_experiance); //array
            $vendorResume->reason_for_leaving = serialize($request->reason_for_leaving); //array
            $vendorResume->date_from = serialize($request->date_from); //array
            $vendorResume->date_to = serialize($request->date_to); //array
            $vendorResume->designation = serialize($request->designation); //array
            $vendorResume->organization = serialize($request->organization); //array
            $vendorResume->project = serialize($project); //array
            $vendorResume->dev_role = serialize($dev_role); //array
            $vendorResume->tools = serialize($tools); //array
            $vendorResume->soft_framework = $request->soft_framework;
            $vendorResume->soft_proficiency = $request->soft_proficiency;
            $vendorResume->soft_description = $request->soft_description;
            $vendorResume->soft_experience = $request->soft_experience;
            $vendorResume->soft_remark = $request->soft_remark;
            $vendorResume->father_name = $request->father_name;
            $vendorResume->dob = $request->dob;
            $vendorResume->gender = $request->gender;
            $vendorResume->marital_status = $request->marital_status;
            $vendorResume->langauge_know = $request->langauge_know;
            $vendorResume->hobbies = $request->hobbies;
            $vendorResume->city = $request->city;
            $vendorResume->state = $request->state;
            $vendorResume->country = $request->country;
            $vendorResume->pin_code = $request->pin_code;
            $vendorResume->address = serialize($request->address);
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
        try{
            $vendorResume = new VendorResume();
            return response()->json(["code" => 200, "data" => $vendorResume, "message" => 'Data stored successfully!!!']);
        } catch (\Exception $e) {
            return response()->json(["code" => 500, "data" => [], "message" => $e->getMessage()]);
        }
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
