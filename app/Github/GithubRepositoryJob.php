<?php

namespace App\Github;

use Illuminate\Database\Eloquent\Model;

class GithubRepositoryJob extends Model
{
    protected $fillable = [
        'id',
        'github_organization_id',
        'github_repository_id',
        'job_name',
    ];

    public function githubOrganization()
    {
        return $this->belongsTo(\App\Github\GithubOrganization::class);
    }

    public function githubRepository()
    {
        return $this->belongsTo(\App\Github\GithubRepository::class);
    }
}
