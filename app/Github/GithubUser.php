<?php

namespace App\Github;

use Illuminate\Database\Eloquent\Model;

class GithubUser extends Model
{
    protected $fillable = [
        'id',
        'username',
        'vendor_id',
        'created_at',
        'updated_at'
    ];

}
