<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Plank\Mediable\Mediable;
class SocialStrategy extends Model
{
    use Mediable;

    protected $fillable = ['social_strategy_subject_id','description','execution_id','content_id','website_id'];

    public function lastChat()
    {
    	return $this->hasOne(ChatMessage::class,'social_strategy_id','id')->orderBy('created_at', 'desc')->latest();
    }

    public function whatsappAll($needBroadcast = false)
    {
        if($needBroadcast) {
            return $this->hasMany('App\ChatMessage', 'social_strategy_id')->where(function($q){
                $q->whereIn('status', ['7', '8', '9', '10'])->orWhere("group_id",">",0);
            })->latest();
        }else{
            return $this->hasMany('App\ChatMessage', 'social_strategy_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
        }
    }
    public function content()
    {
        return $this->hasOne('App\User','id','content_id');
    }

    public function execution()
    {
        return $this->hasOne('App\User','id','execution_id');
    }

}
