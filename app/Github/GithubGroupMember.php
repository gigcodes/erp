<?php

namespace App\Github;

use Illuminate\Database\Eloquent\Model;

class GithubGroupMember extends Model{
    protected $fillable = [
        'github_groups_id',
        'github_users_id'
    ];
}