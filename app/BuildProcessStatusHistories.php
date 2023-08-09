<?php

namespace App;

use App\Models\Project;
use App\Helpers\GithubTrait;
use Illuminate\Database\Eloquent\Model;

class BuildProcessStatusHistories extends Model
{
    use GithubTrait;

    protected $table = 'build_process_status_histories';

    protected $fillable = ['id', 'project_id', 'build_process_history_id', 'build_number', 'old_status', 'status'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
