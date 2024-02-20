<?php

namespace App\Models;

use App\User;
use App\Github\GithubRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GithubTokenHistory extends Model
{
    use HasFactory;

    protected $fillable = ['run_by', 'github_repositories_id', 'github_type', 'token_key', 'details'];

    public function user()
    {
        return $this->belongsTo(User::class, 'run_by')->select('name', 'id');
    }

    public function githubrepository()
    {
        return $this->belongsTo(GithubRepository::class, 'github_repositories_id')->select('name', 'id');
    }
}
