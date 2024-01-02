<?php

namespace App\Loggers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TranslateLog extends Model
{
    use SoftDeletes;

    public static function log($result)
    {
        // Log result to database
        $translatelog = new TranslateLog();
        $translatelog->google_traslation_settings_id = $result['google_traslation_settings_id'];
        $translatelog->messages = $result['messages'];
        $translatelog->error_code = $result['code'];
        $translatelog->domain = $result['domain'];
        $translatelog->reason = $result['reason'];
        $translatelog->save();

        // Return
        return $translatelog;
    }

    public function messages()
    {
        return $this->hasMany(\App\Loggers\FlowLogMessages::class);
    }
}
