<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeploymentVersion extends Model
{
    use HasFactory;

    public $table = 'deployment_versioning';

}
