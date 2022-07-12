<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TaskCategories;
use App\TaskCategory;
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
            $items = TaskCategories::with(["subcategory.task_subject.history"])->get();
            
            echo "<pre>";
            print_r($items->toArray());
            exit;
            //return datatables()->eloquent($items)->toJson();
        }else{
            $title = 'Project Documentation Module';
            return view('Taskcategories.index', ['title' => $title]);
        }  
    }

    public function store(Request $request){
        
        if ($request->ajax()) {
            
           $name = $request['category_name'];
           $user_id = Auth::user()->id;
           $category_array = array(
               "name" =>$name,
           );
           $task_category = TaskCategories::create($category_array);
           $sub_category_array = array(
                'task_category_id'=>$task_category->id,
                'name'=>$request['sub_category_name'],
            );
            
            $task_subcategory = TaskSubCategory::create($sub_category_array);
           $subject1 = array();
            foreach($request['subject'] as $key=>$subject){
              
                $subject_array = array(
                    'task_category_id'=>$task_category->id,
                    'task_subcategory_id'=>$task_subcategory->id,
                    'name'=>$request['subjectname'][$key],
                    'description'=>$subject,
                );
                $subject1[$key] = TaskSubject::create($subject_array);
                
            }
            
           
               foreach($subject1 as $key => $sub){
               
                $task_history = array(
                    'user_id' => $user_id,
                    'task_subject_id'=>$sub->id,
                    'name_before'=>$sub->name,
                    'name_after'=>NULL,
                    'description_before'=>$sub->description,
                    'description_after'=>NULL,
                );
                $history = TaskHistories::create($task_history);
               }
               
            return back();
            //return datatables()->eloquent($items)->toJson();
        }
       
    }
}
