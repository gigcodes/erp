<?php
namespace App\Helpers;

use App\Mail\AddedEvaluatedAnswerSheet;
use App\Models\AccessPage;
use App\Models\Registration;
use App\Models\TestRelatedCourse;
use App\Models\userDevice;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Twilio\Rest\Client;

class CommonHelper
{
    public static function UTCToLocal($dateTime, $format = 'M d Y') {
        return Carbon::parse($dateTime,'UTC')->timezone(config('timezone'))->format($format);
    }
}
