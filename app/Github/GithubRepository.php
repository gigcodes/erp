<?php

namespace App\Github;

use Illuminate\Database\Eloquent\Model;

class GithubRepository extends Model
{
    protected $fillable = [
        'id',
        'name',
        'html',
        'webhook',
        'created_at',
        'updated_at'
    ];

    public function users()
    {
        return $this->hasManyThrough(
            'App\Github\GithubUser',
            'App\Github\GithubRepositoryUser',
            'github_repositories_id',
            'id',
            'id',
            'id'
        );
    }
}
