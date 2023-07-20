<?php

namespace App\Github;

use Illuminate\Database\Eloquent\Model;

class GithubPrActivity extends Model
{
    protected $fillable = [
        'id',
        'github_organization_id',
        'github_repository_id',
        'pull_number',
        'activity_id',
        'user',
        'event',
        'label_name',
        'label_color',
        'created_at',
        'updated_at',
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
