<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Nestable\NestableTrait;

class Category extends Model
{

	use NestableTrait;

	protected $parent = 'parent_id';

    public $fillable = ['title','parent_id','magento_id','show_all_id'];

	/**
	 * Get the index name for the model.
	 *
	 * @return string
	 */
	public function childs() {
		return $this->hasMany('App\Category','parent_id','id') ;
	}

	public static function isParent($id){

		$child_count = DB::table('categories as c')
		                    ->where('parent_id',$id)
							->count();

		return $child_count ? true : false;
	}


	public static function hasProducts($id) {

		$products_count = DB::table('products as p')
		                 ->where('category',$id)
		                 ->count();

		return $products_count ? true : false;

	}

}


