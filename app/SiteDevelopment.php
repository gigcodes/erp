<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SiteDevelopmentCategory;
use App\ChatMessage;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Plank\Mediable\Mediable;

class SiteDevelopment extends Model
{
    use Mediable;

    protected $fillable = ['site_development_category_id','status','title','description','developer_id','website_id'];


    public function category()
    {
    	$this->belongsTo(SiteDevelopmentCategory::class,'id','site_development_category_id');
    }

    public function lastChat()
    {
    	return $this->hasOne(ChatMessage::class,'site_development_id','id')->orderBy('created_at', 'desc')->latest();
    }

    public function whatsappAll($needBroadcast = false)
    {
        if($needBroadcast) {
            return $this->hasMany('App\ChatMessage', 'site_development_id')->where(function($q){
                $q->whereIn('status', ['7', '8', '9', '10'])->orWhere("group_id",">",0);
            })->latest();
        }else{
            return $this->hasMany('App\ChatMessage', 'site_development_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
        }
    }

    public function developer()
    {
        return $this->hasOne('App\User','id','developer_id');
    }

}
