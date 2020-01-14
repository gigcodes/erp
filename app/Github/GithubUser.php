<?php

namespace App\Github;

use Illuminate\Database\Eloquent\Model;

class GithubUser extends Model
{
    protected $fillable = [
        'id',
        'username',
        'user_id',
        'created_at',
        'updated_at'
    ];

    public function platformUser(){
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function repositories(){
        return $this->hasManyThrough(
            'App\Github\GithubRepository',
            'App\Github\GithubRepositoryUser',
            'github_users_id',
            'id',
            'id',
            'github_repositories_id'
        );
    }

}
