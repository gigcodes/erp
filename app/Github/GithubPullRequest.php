<?php

namespace App\Github;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GithubPullRequest extends Model
{
    use HasFactory;

    public $fillable = [
        'pr_number',
        'repo_name',
        'github_repository_id',
        'pr_title',
        'pr_url',
        'state',
        'created_by',
    ];

}
