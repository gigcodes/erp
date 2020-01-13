<?php

namespace App\Github;

use Illuminate\Database\Eloquent\Model;

class GithubGroup extends Model{

    protected $fillable = [
        'id',
        'vendor_id',
        'name'
    ];

    public function vendor(){
        return $this->belongsTo('App\Vendor');
    }

    public function users()
    {
        //return $this->hasManyThrough('App\Github\GithubUser', '');
    }

}