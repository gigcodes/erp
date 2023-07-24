<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeploymentVersionLog extends Model
{
    use HasFactory;

    protected $table = 'deployment_version_logs';

    protected $fillable = ['deployemnet_version_id','user_id','build_number', 'error_message', 'error_code', 'created_at', 'updated_at'];
}
