<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'zoom/webhook',
        'twilio/*',
        'run-webhook/*',
        'whatsapp/*',
        'livechat/*',
        'duty/v1/calculate',
        'hubstaff/linkuser',
        'time-doctor/link_time_doctor_user',
        'calendar',
        'calendar/*',
        'api/wetransfer-file-store',
        'cold-leads-broadcasts',
        'auto-build-process',
    ];
}
