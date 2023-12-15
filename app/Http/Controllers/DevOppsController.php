<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\User;
use App\Models\DevOppsCategories;
use App\Models\DevOppsSubCategory;
use App\Models\DevOppsRemarks;
use Illuminate\Http\Request;
use App\DeveloperTask;
use App\Task;

class DevOppsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $items = DevOppsSubCategory::with(['devoops_category']);

            if (isset($request->category_name) && ! empty($request->category_name)) {
                $items = DevOppsSubCategory::with(['devoops_category'])
                ->whereHas('devoops_category', function ($q) use ($request) {
                    $q->where('dev_opps_categories.name', 'Like', '%' . $request->category_name . '%');
                });
            }
            if (isset($request->sub_category_name) && ! empty($request->sub_category_name)) {
                $items->where('dev_opps_sub_categories.name', 'Like', '%' . $request->sub_category_name . '%');
            }

            return datatables()->eloquent($items)->toJson();
        } else {
            $title = 'Dev Opps Module';

            $devoops_categories = DevOppsCategories::pluck('name', 'id')->prepend('Select category', '');

            $allUsers = User::where('is_active', '1')->select('id', 'name')->orderBy('name')->get();

            return view('dev-oops.index', ['title' => $title, 'devoops_categories' => $devoops_categories, 'allUsers' => $allUsers]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->ajax()) {

            if($request['category_type']==1){
                $name = $request['category_name'];
                $category_array = [
                    'name' => $name,
                ];
                DevOppsCategories::create($category_array);
            } else {

                $sub_category_array = [
                    'devoops_category_id' => $request['devoops_category_id'],
                    'name' => $request['sub_category_name'],
                ];

                DevOppsSubCategory::create($sub_category_array);
            }
            return response()->json(['code' => 200, 'message' => 'Record added Successfully!']);
            //return datatables()->eloquent($items)->toJson();
        }
    }

    public function update(Request $request)
    {
        if ($request->ajax()) {

            if($request['category_type']==1){
                $id = $request->id;
                $categoryname = $request->category_name;
                $category = DevOppsCategories::find($id);

                if (!empty($category)) {
                    $category->name = $categoryname; // Assign the new value to the 'name' attribute
                    $category->save(); // Save the changes to the database
                }
            } else {
                $id = $request->id;
                $sub_category = $request->sub_category_name;
                $devoops_category_id = $request->devoops_category_id;
                $subcategory = DevOppsSubCategory::find($id);
                if (!empty($subcategory)) {
                    $subcategory->name = $sub_category;
                    $subcategory->devoops_category_id = $devoops_category_id;
                    $subcategory->save();
                }
            }
            return response()->json(['code' => 200, 'message' => 'Record updated Successfully!']);
        }
    }

    public function delete($id)
    {   
        $items = DevOppsCategories::find($id);
        $delete = $items->delete();

        return response()->json(['code' => 200, 'message' => 'Record deleted Successfully!']);
    }

    public function subdelete($id)
    {
        $items = DevOppsSubCategory::find($id);
        $delete = $items->delete();

        return response()->json(['code' => 200, 'message' => 'Record deleted Successfully!']);
    }

    public function saveRemarks(Request $request)
    {   

        $post = $request->all();

        $this->validate($request, [
            'main_category_id' => 'required',
            'sub_category_id' => 'required',
            'remarks' => 'required',
        ]);

        $input = $request->except(['_token']);  
        $input['added_by'] = Auth::user()->id;
        DevOppsRemarks::create($input);

        return response()->json(['code' => 200, 'data' => $input]);
    }

    public function getRemarksHistories(Request $request)
    {
        $datas = DevOppsRemarks::with(['user'])
                ->where('main_category_id', $request->main_category_id)
                ->where('sub_category_id', $request->sub_category_id)
                ->latest()
                ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function taskCount($site_developement_id)
    {
        $othertask = Task::where('site_developement_id', $site_developement_id)->where('category', 60)->whereNull('is_completed')->select();
        $query1 = Task::join('users', 'users.id', 'tasks.assign_to')->where('site_developement_id', $site_developement_id)->whereNull('is_completed')->select('tasks.id', 'tasks.task_subject as subject', 'tasks.assign_status', 'users.name as assigned_to_name');
        $query1 = $query1->addSelect(DB::raw("'Othertask' as task_type,'task' as message_type"));
        $othertaskStatistics = $query1->get();

        return response()->json(['code' => 200, 'taskStatistics' => $othertaskStatistics]);
    }
}
