<?php

namespace App;

use App\Models\Project;
use Illuminate\Database\Eloquent\Model;

class BuildProcessErrorLog extends Model
{
    protected $table = 'build_process_error_logs';

    protected $fillable = ['id', 'project_id', 'error_message', 'error_code', 'github_organization_id', 'github_repository_id', 'github_branch_state_name','user_id'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function organization()
    {
        return $this->belongsTo(\App\Github\GithubOrganization::class, 'github_organization_id', 'id');
    }

    public function repository()
    {
        return $this->belongsTo(\App\Github\GithubRepository::class, 'github_repository_id', 'id');
    }

    public static function log($result)
    {
        // Log result to database
        $buildProcessErrorLog = new BuildProcessErrorLog();
        $buildProcessErrorLog->project_id = $result['project_id'];
        $buildProcessErrorLog->error_message = $result['error_message'];
        $buildProcessErrorLog->error_code = $result['error_code'];
        $buildProcessErrorLog->github_organization_id = $result['github_organization_id'];
        $buildProcessErrorLog->github_repository_id = $result['github_repository_id'];
        $buildProcessErrorLog->github_branch_state_name = $result['github_branch_state_name'];
        $buildProcessErrorLog->user_id = $result['user_id'];
        $buildProcessErrorLog->save();

        // Return
        return $buildProcessErrorLog;
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class , 'user_id');
    }
}
