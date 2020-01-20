<?php

namespace App\Github;

use DB;
use Illuminate\Database\Eloquent\Model;

class GithubBranchState extends Model
{
    protected $fillable = [
        'repository_id',
        'branch_name',
        'ahead_by',
        'behind_by'
    ];
}
