<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GitPullRequestErrorLog extends Model
{
    use HasFactory;

    protected $table = 'git_pr_error_logs';

}
