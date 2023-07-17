<?php

namespace App;

use App\Github\GithubRepositoryJob;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\GithubTrait;
use App\Models\Project;

class BuildProcessHistory extends Model
{
    use GithubTrait;

    protected $table = 'build_process_histories';

    protected $fillable = ['id', 'store_website_id', 'status', 'text', 'build_name', 'build_number', 'created_by', 'github_organization_id', 'github_repository_id', 'github_branch_state_name'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'store_website_id', 'id');
    }

    public function organization()
    {
        return $this->belongsTo(\App\Github\GithubOrganization::class, 'github_organization_id', 'id');
    }

    public function repository()
    {
        return $this->belongsTo(\App\Github\GithubRepository::class, 'github_repository_id', 'id');
    }

    public function getJobStatusAttribute()
    {
        $githubRepositoryId = $this->github_repository_id;
        $githubBranchStateName = $this->github_branch_state_name;
        // $githubRepositoryId = 231925646;
        // $githubBranchStateName = "master";
        if(empty($githubRepositoryId) || $githubBranchStateName==''){
            return [];
        }
        $githubActionRuns = $this->getGithubActionRuns($githubRepositoryId, 1, null, null, $githubBranchStateName);
        
        // Get Repo Jobs from DB & Prepare the status. 
        $githubRepositoryJobs = GithubRepositoryJob::where('github_repository_id',  $githubRepositoryId)->pluck('job_name')->toArray();

        if($githubActionRuns->total_count > 0 && isset($githubActionRuns->workflow_runs)) {
            $job_status = [];
            $githubActionRunJobs = $this->getGithubActionRunJobs($githubRepositoryId, $githubActionRuns->workflow_runs[0]->id);
            // Prepareing job status for every actions
            foreach ($githubActionRunJobs->jobs as $job) {
                if(in_array($job->name, $githubRepositoryJobs)) {
                    $job_status[$job->name] = $job->status;
                }
            }
            return $job_status;
        } else {
            return [];
        }
    }
}
