<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Media;
use Plank\Mediable\Mediable;
use DB;

class CroppedImageReference extends Model
{
    public function media() {
        return $this->hasOne(Media::class, 'id', 'original_media_id');
    }

    public function newMedia() {
        return $this->hasOne(Media::class, 'id', 'new_media_id');
    }

    public function productCategory($media){
    	$media = DB::table('mediables')->where('mediable_type','App\Product')->where('media_id',$media)->first();
    	
    	$product = Product::select('category')->where('id',$media->mediable_id)->first();
    	return $product->product_category->title;
    }

    public function productSupplier($media){
    	$media = DB::table('mediables')->where('mediable_type','App\Product')->where('media_id',$media)->first();
    	
    	$product = Product::select('supplier')->where('id',$media->mediable_id)->first();
    	return $product->supplier;
    }

    public function productBrand($media){
    	$media = DB::table('mediables')->where('mediable_type','App\Product')->where('media_id',$media)->first();
    	
    	$product = Product::select('brand')->where('id',$media->mediable_id)->first();
    	
    	return $product->brands->name;
    }

    public function productStatus($media){
    	$media = DB::table('mediables')->where('mediable_type','App\Product')->where('media_id',$media)->first();
    	
    	$product = Product::select('status_id')->where('id',$media->mediable_id)->first();
    	return $product->status_id;
    }

    public function getProductIssueStatus($id){
    	$task = DeveloperTask::where('task','LIKE','%'.$id.'%')->first();
    	if($task != null){
    		if($task->status == 'done'){
    			return 'Issue Resolved';
    		}else{
    			return 'Issue Pending';
    		}
    	}else{
    		return 'No Issue Yet';
    	}
	}





}
