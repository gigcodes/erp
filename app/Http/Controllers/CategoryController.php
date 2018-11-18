<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{


	public function __construct() {
		$this->middleware('permission:category-edit',['only' => ['addCategory','edit','manageCategory','remove']]);
	}
	//
	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function manageCategory(Request $request)
	{
		$categories = Category::where('parent_id', '=', 0)->get();
		$allCategories = Category::pluck('title','id')->all();

		$old = $request->old('parent_id');

		$allCategoriesDropdown = Category::attr(['name' => 'parent_id','class' => 'form-control'])
		                            ->selected( $old ? $old : 1)
		                            ->renderAsDropdown();


		$allCategoriesDropdownEdit = Category::attr(['name' => 'edit_cat','class' => 'form-control'])
		                                 ->selected( $old ? $old : 1)
		                                 ->renderAsDropdown();

		return view('category.treeview',compact('categories','allCategories','allCategoriesDropdown','allCategoriesDropdownEdit'));
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function addCategory(Request $request)
	{
		$this->validate($request, [
			'title' => 'required',
			'magento_id' => 'required|numeric',
			'show_all_id' => 'numeric|nullable',
		]);
		$input = $request->all();
		$input['parent_id'] = empty($input['parent_id']) ? 0 : $input['parent_id'];

		Category::create($input);
		return back()->with('success', 'New Category added successfully.');
	}

	public function edit(Category $category,Request $request){

		$data = [];
		$data['id'] = $category->id;
		$data['title'] = $category->title;
		$data['magento_id'] = $category->magento_id;
		$data['show_all_id'] = $category->show_all_id;

		if( $request->method() === 'POST' )
		{
			$this->validate($request, [
				'title' => 'required',
				'magento_id' => 'required|numeric',
				'show_all_id' => 'numeric|nullable',
			]);

			$category->title = $request->input('title');
			$category->magento_id = $request->input('magento_id');
			$category->show_all_id = $request->input('show_all_id');
			$category->save();

			return redirect()->route('category')
			                 ->with('success-remove','Category updated successfully');
		}

		return view('category.edit',$data);
	}

	public function remove(Request $request){

		$category_instance = new Category();
		$category = $category_instance->find($request->input('edit_cat'));

		if( Category::isParent($category->id) )
			return back()->with('error-remove', 'Can\'t delete Parent category. Please delete all the childs first');

		if( Category::hasProducts($category->id))
			return back()->with('error-remove', 'Can\'t delete category is associated with products. Please remove all the association first');

		if( $category->id == 1)
			return back()->with('error-remove', 'Can\'t be delete');

		$title = $category->title;
		$category->delete();

		return back()->with('success-remove', $title.' category Deleted');
	}

	public static function getCategoryTree($id){

		$category = new Category();
		$category_instance = $category->find($id);
		$categoryTree = [];

		$categoryTree[] = $category_instance->title;
		$parent_id = $category_instance->parent_id;

		while ($parent_id != 0){

			$category_instance = $category->find($parent_id);
			$categoryTree[] = $category_instance->title;
			$parent_id = $category_instance->parent_id;
		}

		return array_reverse($categoryTree);
	}

	public static function getCategoryTreeMagentoIds($id){

		$category = new Category();
		$category_instance = $category->find($id);
		$categoryTree = [];

		$categoryTree[] = $category_instance->magento_id;
		$parent_id = $category_instance->parent_id;

		while ($parent_id != 0){

			$category_instance = $category->find($parent_id);
			$categoryTree[] = $category_instance->magento_id;

			if(!empty($category_instance->show_all_id))
			$categoryTree[] = $category_instance->show_all_id;

			$parent_id = $category_instance->parent_id;
		}

		//Adding root category.
//		array_push($categoryTree,'2');

		return array_reverse($categoryTree);
	}

	public static function getCategoryIdByName($term){
		$category = Category::where('title', '=' ,$term)->first();

		return $category ? $category->id : 0;
	}

}
