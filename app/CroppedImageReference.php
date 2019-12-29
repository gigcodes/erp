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

    public function product()
    {
    	return $this->hasOne(Product::class,'id','product_id');
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
