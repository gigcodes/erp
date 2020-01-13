<?php

namespace App\Github;

use Illuminate\Database\Eloquent\Model;

class GithubGroup extends Model{

    protected $fillable = [
        'id',
        'name'
    ];



    public function users()
    {
        //return $this->hasManyThrough('App\Github\GithubUser', '');
    }

}