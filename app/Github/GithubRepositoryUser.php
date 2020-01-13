<?php

namespace App\Github;

use Illuminate\Database\Eloquent\Model;

class GithubRepositoryUser extends Model{

    protected $fillable = [
        'id',
        'github_repositories_id',
        'github_users_id',
        'rights'
    ];



}