<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Uicheck extends Model
{
    protected $table = 'uichecks';

    protected $fillable = ['id','site_development_category_id', 'website_id', 'issue', 'communication_message','dev_status_id', 'admin_status_id'];

    public function whatsappAll($needBroadCast = false)
    {
    	if($needBroadCast) {
            return $this->hasMany('App\ChatMessage', 'document_id')->whereIn('status', ['7', '8', '9', '10'])->latest();    
        }

        return $this->hasMany('App\ChatMessage', 'document_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
	}
}
