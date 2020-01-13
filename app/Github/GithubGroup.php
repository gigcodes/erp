<?php

namespace App\Github;

use Illuminate\Database\Eloquent\Model;

class GithubGroup extends Model{

    protected $fillable = [
        'id',
        'vendor_id',
        'name'
    ];

}