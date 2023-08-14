<?php

namespace App\Github;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GithubOrganization extends Model
{
    use HasFactory;

    // RELATIONS
    public function repos()
    {
        return $this->hasMany('App\Github\GithubRepository', 'github_organization_id', 'id');
    }
}
