<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LaravelGithubLog extends Model
{
    protected $fillable = [
        'log_time',
        'log_file_name',
        'file',
        'author',
        'commit_time',
        'commit',
        'stacktrace'
    ];

    
}
