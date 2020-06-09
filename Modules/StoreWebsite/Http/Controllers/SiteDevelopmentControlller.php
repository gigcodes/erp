<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\StoreWebsite;
use App\SiteDevelopment;
use App\SiteDevelopmentCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use App\Setting;
use App\Role;
use DB;
use App\User;

class SiteDevelopmentController extends Controller
{

	public function index($id = null, Request $request)
	{

		//Getting Website Details 
		$website = StoreWebsite::find($id);

		$categories =  SiteDevelopmentCategory::orderBy('id','desc')->paginate(Setting::get('pagination'));

		//Getting Roles Developer
		$role = Role::where('name','LIKE','%Developer%')->first();
		
		//User Roles with Developers
		$roles = DB::table('role_user')->select('user_id')->where('role_id',$role->id)->get();
		
		foreach ($roles as $role) {
			$userIDs[] = $role->user_id; 
		}
		
		if(!isset($userIDs)){
			$userIDs = [];
		}

		$users = User::select('id','name')->whereIn('id',$userIDs)->get();
		if ($request->ajax()) {
	      return response()->json([
	        'tbody' => view('storewebsite::site-development.partials.data', compact('categories','users','website'))->render(),
	        'links' => (string) $categories->render()
	      ], 200);
	    }

	    $allStatus = \App\SiteDevelopmentStatus::pluck("name","id")->toArray();

		return view('storewebsite::site-development.index', compact('categories','users','website','allStatus'));
	}


	public function addCategory(Request $request)
	{
		if($request->text){

			//Cross Check if title is present
			$categoryCheck = SiteDevelopmentCategory::where('title',$request->text)->first();
			
			if(empty($categoryCheck)){
				//Save the Category
				$develop = new SiteDevelopmentCategory;
				$develop->title = $request->text;
				$develop->save();

				return response()->json(["code" => 200,"messages" => 'Category Saved Sucessfully']);
			
			}else{
				
				return response()->json(["code" => 500,"messages" => 'Category Already Exist']);
			}
			
		}else{
			return response()->json(["code" => 500,"messages" => 'Please Enter Text']);
		}
	}

	public function addSiteDevelopment(Request $request)
	{
		
		if($request->site){
			$site =  SiteDevelopment::find($request->site);
		}else{
			$site = new SiteDevelopment;
		}
		
		if($request->type == 'title'){
			$site->title = $request->text;
		}

		if($request->type == 'description'){
			$site->description = $request->text;
		}

		if($request->type == 'status'){
			$site->status = $request->text;
		}

		if($request->type == 'developer'){
			$site->developer_id = $request->text;
		}

		$site->site_development_category_id = $request->category;
		$site->website_id = $request->websiteId;
		
		$site->save();

		return response()->json(["code" => 200,"messages" => 'Site Development Saved Sucessfully']);
		
	}


	public function editCategory(Request $request)
	{
		
		$category = SiteDevelopmentCategory::find($request->categoryId);
		if($category){
			$category->title = $request->category;
			$category->save();
		}

		return response()->json(["code" => 200,"messages" => 'Category Edited Sucessfully']);
	}


}