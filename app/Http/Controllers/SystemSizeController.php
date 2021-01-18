<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SystemSize;
use App\SystemSizeManager;
use App\Setting;
use App\Category;
use Response;

class SystemSizeController extends Controller
{
    public function index(){
    	$systemSizesManagers = SystemSizeManager::select(
	    												'system_size_managers.id',
	    												'categories.title as category',
	    												'system_sizes.name as country',
	    												'system_size_managers.size',
	    												'system_size_managers.created_at',
	    												'system_size_managers.created_at',
    												)
    												->leftjoin('categories','categories.id','system_size_managers.category_id')
    												->leftjoin('system_sizes','system_sizes.id','system_size_managers.system_size_id')
    												->where('system_size_managers.status',1)
    												->paginate(Setting::get('pagination'));
    	$systemSizes = SystemSize::where('status',1)->get();
    	$parentCategories = Category::where('parent_id',0)->get();
    	$categories = [];
    	
    	foreach ($parentCategories as $key => $value) {
    		$tempCat['parentcategory'] = $value->title;
    		$tempCat['subcategories'] = Category::where('parent_id',$value->id)->get();
    		$categories[] = $tempCat;
    	}

    	return view('system-size.index',compact('systemSizes','systemSizesManagers','categories'));
    }
    public function store(Request $request){
    	$this->validate($request, [
            'name' => 'required',
        ]);

        SystemSize::create(['name' => $request->input('name')]);
        return response()->json(['success' => true, 'message' => "System size created successfully"]);
    }
    public function update(Request $request){
    	$this->validate($request, [
            'code' => 'required',
        ]);

        $systemsize = SystemSize::find($request->input('id'));
        $systemsize->name = $request->input('code');
        if ($systemsize->save()) {
        	return response()->json(['success' => true, 'message' => "System size update successfully"]);
        }
        return response()->json(['success' => false, 'message' => "Something went wrong!"]);
    }
    public function delete(Request $request){
        $systemsize = SystemSize::find($request->input('id'));
        $systemsize->status = 0;
        if ($systemsize->save()) {
        	return response()->json(['success' => true, 'message' => "System size delete successfully"]);
        }
        return response()->json(['success' => false, 'message' => "Something went wrong!"]);
    }
    public function managerstore(Request $request){
    	foreach ($request->sizes as $key => $value) {
    		SystemSizeManager::create([
    									'category_id' => $request->category,
    									'system_size_id' => $value['system_size_id'],
    									'size' => $value['size'],
    								]);
    	}
       	return response()->json(['success' => true, 'message' => "System size saved successfully"]);
    }
    public function managerupdate(Request $request){
    	$sm = SystemSizeManager::find($request->input('id'));
    	$sm->size = $request->input('size');
    	if ($sm->save()) {
        	return response()->json(['success' => true, 'message' => "Update successfully!"]);
    	}
    	return response()->json(['success' => false, 'message' => "Something went wrong!"]);
    }
    public function managerdelete(Request $request){
    	$sm = SystemSizeManager::find($request->input('id'));
    	$sm->status = 0;
    	if ($sm->save()) {
        	return response()->json(['success' => true, 'message' => "Delete successfully!"]);
    	}
    	return response()->json(['success' => false, 'message' => "Something went wrong!"]);
    }
    public function managercheckexistvalue(Request $request){
    	$sm = SystemSizeManager::where('category_id',$request->id)->get()->pluck('system_size_id')->toArray();
    	$systemSizes = SystemSize::where('status',1)->get();
    	$html = '';
    	foreach($systemSizes as $systemSize){
		    if (!in_array($systemSize->id, $sm)) {
		    	$html .= '<div class="col-md-12 mt-3 sizevarintinput"><div class="row"><div class="col-md-4"><span>'.$systemSize->name.'</span></div><div class="col-md-8"><input type="text" class="form-control" placeholder="Enter size" name="sizes['.$systemSize->id.'][size]" required=""><input type="hidden" name="sizes['.$systemSize->id.'][system_size_id]" value="'.$systemSize->id.'"></div></div></div>';
		    }
	    }
	    if ($html == '') {
	    	$html = '<div class="col-md-12 mt-3 sizevarintinput text-center"> No size variant left!</div>';
	    }
	    return response()->json(['success' => true, 'message' => "successful!", 'data' => $html]);
    }
}
