<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitorJenkinsBuild extends Model
{
    use HasFactory;

    protected $fillable = [
        'build_number',
        'project',
        'worker',
        'store_id',
        'clone_repository',
        'lock_build',
        'update_code',
        'composer_install',
        'make_config',
        'setup_upgrade',
        'compile_code',
        'static_content',
        'reindexes',
        'magento_cache_flush',
        'error',    
        'build_status', 
        'full_log',    
        'meta_update',    
    ];
}
