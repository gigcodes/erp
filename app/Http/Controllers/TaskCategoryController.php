<?php

namespace App\Http\Controllers;

use App\TaskCategory;
use Illuminate\Http\Request;

class TaskCategoryController extends Controller
{
	public function __construct() {

	}

	public function index(){

		$data = TaskCategory::latest()->get();
		return view('task-module.category.index',compact('data'));
	}

	public function create(){

		$data = [];
		$data['title'] = '';
		$data['modify'] = 0;

		return view('task-module.category.form',$data);
	}

	public function edit(TaskCategory $task_category){

		$data = $task_category->toArray();
		$data['modify'] = 1;

		return view('task-module.category.form',$data);
	}

	public function store(Request $request){

		$this->validate($request,[
			'title' => 'required_without:subcategory'
		]);

		if ($request->name != '') {
			TaskCategory::create(['title' => $request->title]);
		}

		if ($request->parent_id != '' && $request->subcategory != '') {
			TaskCategory::create(['title' => $request->subcategory, 'parent_id' => $request->parent_id]);
		}


		return redirect()->back()->with('success','Category created successfully');
	}

	public function update(Request $request,TaskCategory $task_category){

		$this->validate($request,[
			'title' => 'required'
		]);

		$task_category->update($request->all());

		return redirect()->route('task_category.index')->with('success','Category udpated successfully');
	}

	public function destroy(TaskCategory $task_category){

		$task_category->delete();

		return redirect()->route('task_category.index')->with('success','Category deleted successfully');
	}

	public static function getAllTaskCategory(){

		$task_category = TaskCategory::all()->toArray();
		$task_category_new = [];

		foreach($task_category as $item)
			$task_category_new[$item['id']] = $item['title'];

		return $task_category_new;
	}

	public static function getCategoryNameById($id){

		$task_category = self::getAllTaskCategory();

		if(!empty($task_category[$id]))
			return $task_category[$id];

		return '';
	}
}
