<?php

namespace App;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;

class StoreWebsiteUserHistory extends Model
{
	protected $table = "store_website_user_history";
    
    protected $fillable = [
        'store_website_id', 
        'store_website_user_id', 
        'model', 
        'attribute', 
        'old_value',
        'new_value',
        'user_id'
    ];

    public function storewebsite()
    {
        return $this->belongsTo('App\StoreWebsite','store_website_id','id');
    }

    public function websiteuser()
    {
        return $this->belongsTo('App\StoreWebsiteUsers','store_website_user_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
}
