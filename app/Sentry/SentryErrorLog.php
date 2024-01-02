<?php

namespace App\Sentry;

use Illuminate\Database\Eloquent\Model;

class SentryErrorLog extends Model
{
    protected $fillable = [
        'error_id',
        'error_title',
        'issue_type',
        'issue_category',
        'is_unhandled',
        'first_seen',
        'last_seen',
        'project_id',
        'total_events',
        'total_user',
        'device_name',
        'os',
        'os_name',
        'release_version',
        'status_id',
    ];

    public function sentry_project()
    {
        return $this->belongsTo(\App\Sentry\SentryAccount::class, 'project_id');
    }
}
