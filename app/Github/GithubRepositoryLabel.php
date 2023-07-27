<?php

namespace App\Github;

use Illuminate\Database\Eloquent\Model;

class GithubRepositoryLabel extends Model
{
    protected $fillable = [
        'id',
        'github_organization_id',
        'github_repository_id',
        'label_name',
        'label_color',
        'message',
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
