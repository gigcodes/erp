<?php

namespace App\Github;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GithubTaskPullRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'github_task_id',
        'github_organization_id',
        'github_repository_id',
        'pull_number',
        'created_at',
        'updated_at',
    ];

    public function githubTask()
    {
        return $this->belongsTo(GithubTask::class);
    }
}
