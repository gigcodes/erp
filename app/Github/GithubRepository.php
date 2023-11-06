<?php

namespace App\Github;

use Illuminate\Database\Eloquent\Model;

class GithubRepository extends Model
{
    protected $fillable = [
        'id',
        'github_organization_id',
        'name',
        'html',
        'webhook',
        'token_key',
        'github_type',
    ];

    public function users()
    {
        return $this->belongsToMany(
            \App\Github\GithubUser::class,
            'github_repository_users',
            'github_repositories_id',
            'github_users_id'
        )
            ->withPivot(['id', 'rights']);
    }

    public function branches()
    {
        return $this->hasMany(
            \App\Github\GithubBranchState::class,
            'repository_id',
            'id'
        )->orderBy('last_commit_time', 'desc');
    }

    public function organization()
    {
        return $this->belongsTo(\App\Github\GithubOrganization::class, 'github_organization_id', 'id');
    }
}
