<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BuildProcessHistory extends Model
{
    protected $table = 'build_process_histories';

    protected $fillable = ['id', 'store_website_id', 'status', 'text', 'build_name', 'build_number', 'created_by', 'github_organization_id', 'github_repository_id', 'github_branch_state_name'];

    public function organization()
    {
        return $this->belongsTo(\App\Github\GithubOrganization::class, 'github_organization_id', 'id');
    }

    public function repository()
    {
        return $this->belongsTo(\App\Github\GithubRepository::class, 'github_repository_id', 'id');
    }
}
