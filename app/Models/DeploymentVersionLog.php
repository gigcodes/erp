<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeploymentVersionLog extends Model
{
    use HasFactory;

    protected $table = 'deployment_version_logs';

    protected $fillable = ['deployement_version_id', 'user_id', 'build_number', 'error_message', 'error_code', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function deployversion()
    {
        return $this->belongsTo(DeploymentVersion::class, 'deployement_version_id');
    }
}
