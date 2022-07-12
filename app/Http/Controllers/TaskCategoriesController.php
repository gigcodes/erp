<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TaskCategories;
use App\TaskSubCategory;
use App\TaskSubject;
use App\TaskHistories;
use Auth;
use Illuminate\Support\Facades\DB;


class TaskCategoriesController extends Controller
{
    //
    public function index(Request $request){
        if ($request->ajax()) {
            
            //return datatables()->eloquent($items)->toJson();
        }else{
            $title = 'Project Documentation Module';
            return view('Taskcategories.index', ['title' => $title]);
        }  
    }

    public function store(Request $request){
        
        if ($request->ajax()) {
            dd($request->all());
           $name = $request['category_name'];
           $user_id = Auth::user()->id;
           $category_array = array(
               "name" =>$name,
           );
           $task_category = TaskCategories::create($category_array);
           
        
                $sub_category_array = array(
                    'task_category_id'=>$task_category->id,
                    'name'=>$request['sub_category'],
                );
            
            $task_subcategory = TaskSubCategory::create($sub_category_array);
            foreach($request['subject'] as $key=>$subject){
                $subject_array = array(
                    'task_category_id'=>$task_category->id,
                    'name'=>$sub_category[$key],
                );
            }
            //return datatables()->eloquent($items)->toJson();
        }
       
    }
}
