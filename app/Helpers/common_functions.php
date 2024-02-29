<?php

use App\ErpLog;
use Carbon\Carbon;

function printStatusView()
{
}

function changeTimeZone($dateString, $timeZoneSource = null, $timeZoneTarget = null)
{
    if (empty($timeZoneSource)) {
        $timeZoneSource = date_default_timezone_get();
    }
    if (empty($timeZoneTarget)) {
        $timeZoneTarget = date_default_timezone_get();
    }

    $dt = new DateTime($dateString, new DateTimeZone($timeZoneSource));
    $dt->setTimezone(new DateTimeZone($timeZoneTarget));

    return $dt->format('Y-m-d H:i:s');
}

/**
 * Create image and text
 *
 * @param mixed $path
 * @param mixed $uploadPath
 * @param mixed $text
 * @param mixed $color
 * @param mixed $fontSize
 * @param mixed $needAbs
 */
function createProductTextImage($path, $uploadPath = '', $text = '', $color = '545b62', $fontSize = '40', $needAbs = true)
{
    $text = wordwrap(strtoupper($text), 24, "\n");

    $img = \IImage::make($path);
    $img->resize(600, null, function ($constraint) {
        $constraint->aspectRatio();
    });
    // use callback to define details
    $img->text($text, 5, 50, function ($font) use ($fontSize, $color) {
        $font->file(public_path('/fonts/HelveticaNeue.ttf'));
        $font->size($fontSize);
        $font->color('#' . $color);
        $font->align('top');
    });

    $name = round(microtime(true) * 1000) . '_watermarked';

    if (! file_exists(public_path('uploads' . DIRECTORY_SEPARATOR . $uploadPath . DIRECTORY_SEPARATOR))) {
        mkdir(public_path('uploads' . DIRECTORY_SEPARATOR . $uploadPath . DIRECTORY_SEPARATOR), 0777, true);
    }

    $path = 'uploads' . DIRECTORY_SEPARATOR . $uploadPath . DIRECTORY_SEPARATOR . $name . '.jpg';

    $img->save(public_path($path));

    return ($needAbs) ? public_path($path) : url('/') . '/' . $path;
}

function get_folder_number($id)
{
    return floor($id / config('constants.image_per_folder'));
}

function previous_sibling(array $elements, $previous_sibling = 0, &$branch = [])
{
    foreach ($elements as $k => $element) {
        if ($element['previous_sibling'] == $previous_sibling && $previous_sibling != 0) {
            $branch[] = $element;
            previous_sibling($elements, $element['id'], $branch);
        }
    }

    return $branch;
}

/**
 * return all types of short message with postfix
 *
 * @param mixed $message
 * @param mixed $size
 * @param mixed $postfix
 */
function show_short_message($message, $size = 50, $postfix = '...')
{
    $message = trim($message);

    $dot = '';

    if (strlen($message) > $size) {
        $dot = $postfix;
    }

    return substr($message, 0, $size) . $dot;
}

/**
 * key is using for to attach customer via session
 */
function attach_customer_key()
{
    return 'customer_list_' . time() . '_' . auth()->user()->id;
}

/**
 *  get scraper last log file name
 *
 * @param mixed $screaperName
 * @param mixed $serverId
 */
function get_server_last_log_file($screaperName = '', $serverId = '')
{
    $d = date('j', strtotime('-1 days'));

    return '/scrap-logs/file-view/' . $screaperName . '-' . $d . '.log/' . $serverId;
}

function getStartAndEndDate($week, $year)
{
    $dto = new DateTime();
    $dto->setISODate($year, $week);
    $ret['week_start'] = $dto->format('Y-m-d');
    $dto->modify('+7 days');
    $ret['week_end'] = $dto->format('Y-m-d');

    return $ret;
}

/**
 * Moved function from chat api to here due to duplicates
 */
if (! function_exists('getInstance')) {
    function getInstance($number)
    {
        $number = ! empty($number) ? $number : 0;

        return isset(config('apiwha.instances')[$number])
            ? config('apiwha.instances')[$number]
            : config('apiwha.instances')[0];
    }
}

function human_error_array($errors)
{
    $list = [];
    if (! empty($errors)) {
        foreach ($errors as $key => $berror) {
            foreach ($berror as $serror) {
                $list[] = "{$key} : " . $serror;
            }
        }
    }

    return $list;
}

/**
 * Get all instances no with array list
 */
if (! function_exists('getInstanceNo')) {
    function getInstanceNo()
    {
        $nos = config('apiwha.instances');

        $list = [];

        if (! empty($nos)) {
            foreach ($nos as $key => $no) {
                $n        = ($key == 0) ? $no['number'] : $key;
                $list[$n] = $n;
            }
        }

        return $list;
    }
}

/**
 * Check if the date is valid
 *
 * @param mixed $date
 * @param mixed $format
 */
function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);

    return $d && $d->format($format) === $date;
}

/**
 * dropdown returns in helpers
 */
function drop_down_frequency()
{
    return [
        '0'    => 'Disabled',
        '1'    => 'Just Once',
        '5'    => 'Every 5 Minutes',
        '10'   => 'Every 10 Minutes',
        '15'   => 'Every 15 Minutes',
        '20'   => 'Every 20 Minutes',
        '25'   => 'Every 25 Minutes',
        '30'   => 'Every 30 Minutes',
        '35'   => 'Every 35 Minutes',
        '40'   => 'Every 40 Minutes',
        '45'   => 'Every 45 Minutes',
        '50'   => 'Every 50 Minutes',
        '55'   => 'Every 55 Minutes',
        '60'   => 'Every Hour',
        '360'  => 'Every 6 hr',
        '1440' => 'Every 24 hr',
    ];
}

/**
 * format the duration in Hour:minute:seconds format
 *
 * @param mixed $seconds_time
 */
function formatDuration($seconds_time)
{
    if ($seconds_time < 24 * 60 * 60) {
        return gmdate('H:i:s', $seconds_time);
    } else {
        $hours   = floor($seconds_time / 3600);
        $minutes = floor(($seconds_time - $hours * 3600) / 60);
        $seconds = floor($seconds_time - ($hours * 3600) - ($minutes * 60));

        return "$hours:$minutes:$seconds";
    }
}

function get_field_by_number($no, $field = 'name')
{
    $no = explode('@', $no);

    if (! empty($no[0])) {
        $customer = \App\Customer::where('phone', $no[0])->first();
        if ($customer) {
            return $customer->{$field} . ' (Customer)';
        }

        $vendor = \App\Vendor::where('phone', $no[0])->first();
        if ($vendor) {
            return $vendor->{$field} . ' (Vendor)';
        }

        $supplier = \App\Supplier::where('phone', $no[0])->first();
        if ($supplier) {
            return $supplier->{$field} . '(Supplier)';
        }
    }

    return '';
}

function splitTextIntoSentences($text)
{
    return preg_split('/(?<=[.?!])\s+(?=[a-z])/i', $text);
}

function isJson($string)
{
    json_decode($string);

    return json_last_error() == JSON_ERROR_NONE;
}

function array_find($needle, array $haystack)
{
    foreach ($haystack as $key => $value) {
        if (false !== stripos($value, $needle)) {
            return true;
        }
    }

    return false;
}

function GUID()
{
    if (function_exists('com_create_guid') === true) {
        return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}

function replace_dash($string)
{
    $string = str_replace(' ', '_', strtolower($string)); // Replaces all spaces with hyphens.
    $string = str_replace('-', '_', strtolower($string)); // Replaces all spaces with hyphens.

    return preg_replace('/\s+/', '_', strtolower($string));
}

function replaceSpaceWithDash($string)
{
    $string = str_replace(' ', '-', strtolower($string)); // Replaces all spaces with hyphens.

    return preg_replace('/\s+/', '-', strtolower($string));
}

function storeERPLog($erpData)
{
    if (! empty($erpData)) {
        $erpData['request']  = json_encode($erpData['request']);
        $erpData['response'] = json_encode($erpData['response']);
        ErpLog::create($erpData);
    }
}

function getStr($srt)
{
    preg_match("/\[(.*)\]/", $srt, $matches);
    if ($matches && $matches[0] !== '') {
        return true;
    }

    return false;
}

function string_convert($msg2)
{
    return explode('||', $msg2);
}

function convertToThumbUrl($url, $extension)
{
    $arr                  = explode('/', $url);
    $arr[count($arr) - 1] = 'thumbnail/' . $arr[count($arr) - 1];

    $converted_str = implode('/', $arr);

    return str_replace('.' . $extension, '_thumb.' . $extension, $converted_str); // if product name is abc.jpg than thumb url name is abc_thumb.jpg name with in /thumbnaiil folder of relateable folder path.
}

function resizeCropImage($max_width, $max_height, $source_file, $dst_dir = null, $quality = 80)
{
    if ($dst_dir === null) {
        $dst_dir = $source_file;
    }
    $imgsize = getimagesize($source_file);
    $width   = $imgsize[0];
    $height  = $imgsize[1];
    $mime    = $imgsize['mime'];

    switch ($mime) {
        case 'image/gif':
            $image_create = 'imagecreatefromgif';
            $image        = 'imagegif';
            break;

        case 'image/png':
            $image_create = 'imagecreatefrompng';
            $image        = 'imagepng';
            $quality      = 7;
            break;

        case 'image/jpeg':
            $image_create = 'imagecreatefromjpeg';
            $image        = 'imagejpeg';
            $quality      = 80;
            break;

        default:
            return false;
            break;
    }

    $dst_img = imagecreatetruecolor($max_width, $max_height);
    $src_img = $image_create($source_file);

    imagealphablending($dst_img, false);
    imagesavealpha($dst_img, true);

    $width_new  = $height * $max_width / $max_height;
    $height_new = $width * $max_height / $max_width;
    //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
    if ($width_new > $width) {
        //cut point by height
        $h_point = (($height - $height_new) / 2);
        //copy image
        $imagecopyresampled = imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
    // return true;
    } else {
        //cut point by width
        $w_point            = (($width - $width_new) / 2);
        $imagecopyresampled = imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
        // return true;
    }
    $image($dst_img, $dst_dir, $quality);

    if ($dst_img) {
        $imagedestroy = imagedestroy($dst_img);
    }

    if ($src_img) {
        $imagedestroy = imagedestroy($src_img);
    }

    return @file_get_contents($dst_dir);
}

function _p($data, $exit = 0)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    echo '<pre>______________________________________________________________________________________________________________</pre>';
    if ($exit) {
        exit('');
    }
}

function _pq($q, $exit = 0)
{
    echo '<pre>';
    print_r($q->toSql());
    echo '</pre>';
    echo '<pre>';
    print_r($q->getBindings());
    echo '</pre>';
    echo '<pre>______________________________________________________________________________________________________________</pre>';
    if ($exit) {
        exit('');
    }
}

function dateRangeArr($stDate, $enDate)
{
    $data = [];
    while ($stDate <= $enDate) {
        $data[] = [
            'date' => $stDate,
            'day'  => strtolower(date('l', strtotime($stDate))),
        ];
        $stDate = date('Y-m-d', strtotime($stDate . '+1 day'));
    }

    return $data;
}

function pad0($curr)
{
    return $curr < 10 ? '0' . $curr : $curr;
}

function nextHour($curr)
{
    $curr++;
    if ($curr == 24) {
        $curr = '0';
    }

    return $curr < 10 ? '0' . $curr : $curr;
}

function hourlySlots($stTime, $enTime, $lunchTime = null)
{
    $slots = [];
    if ($stTime < $enTime) {
        $stTime = date('Y-m-d H:i:00', strtotime($stTime));
        $enTime = date('Y-m-d H:i:00', strtotime($enTime));
    } else {
        $stTime = date('Y-m-d H:i:00', strtotime($stTime));
        $enTime = date('Y-m-d H:i:00', strtotime($enTime . ' + 1 day'));
    }

    if ($stTime >= $lunchTime && $lunchTime <= $enTime) {
        if ($lunchTime < date('H:i:00', strtotime($stTime))) {
            $lunchTime = date('Y-m-d H:i:00', strtotime($lunchTime . ' + 1 day'));
        } else {
            $lunchTime = date('Y-m-d H:i:00', strtotime($lunchTime));
        }
    }

    if ($lunchTime && ($stTime <= $lunchTime && $lunchTime <= $enTime)) {
        $stTime1 = $stTime;
        $enTime1 = date('Y-m-d H:i:00', strtotime($lunchTime));
        $slots   = array_merge_recursive($slots, hourlySlots($stTime1, $enTime1));
        $stTime  = date('Y-m-d H:i:00', strtotime($lunchTime . ' +1 hour'));

        $temp = hourlySlots($lunchTime, $stTime);
        foreach ($temp as $key => $value) {
            $temp[$key]['type'] = 'LUNCH';
        }
        $slots = array_merge_recursive($slots, $temp);

        $slots = array_merge_recursive($slots, hourlySlots($stTime, $enTime));
    } else {
        while ($stTime < $enTime) {
            $stSlot = $stTime;
            $enSlot = date('Y-m-d H:i:00', strtotime($stSlot . ' +1 hour'));
            if ($enSlot > $enTime) {
                $enSlot = $enTime;
            }
            $diff    = strtotime($enSlot) - strtotime($stSlot);
            $slots[] = [
                'st'   => $stSlot,
                'en'   => $enSlot,
                'mins' => round($diff / 60),
                'type' => 'AVL',
            ];
            // hourlySlots
            $stTime = date('Y-m-d H:i:00', strtotime($stTime . ' +1 hour'));
        }
    }

    return $slots;
}

function getHourlySlots($stTime, $enTime)
{
    $return = [];
    if (date('Y-m-d', strtotime($stTime)) != date('Y-m-d', strtotime($enTime))) {
        $st1    = $stTime;
        $en1    = date('Y-m-d 23:59:59', strtotime($stTime));
        $return = array_merge_recursive($return, getHourlySlots($st1, $en1));

        $st1    = date('Y-m-d 00:00:00', strtotime($enTime));
        $en1    = $enTime;
        $return = array_merge_recursive($return, getHourlySlots($st1, $en1));
    } else {
        while ($stTime < $enTime) {
            $stSlot = $stTime;
            $enSlot = date('Y-m-d H:i:00', strtotime($stSlot . ' +1 hour'));
            if ($enSlot > $enTime) {
                $enSlot = $enTime;
            }
            $enSlot   = date('Y-m-d H:i:00', strtotime($enSlot . ' -1 minute'));
            $return[] = [
                'st'   => $stSlot,
                'en'   => $enSlot,
                'mins' => round((strtotime($enSlot) - strtotime($stSlot)) / 60),
            ];
            $stTime = date('Y-m-d H:i:00', strtotime($stTime . ' +1 hour'));
        }
    }

    return $return;
}

function siteJs($path)
{
    return env('APP_URL') . '/js/pages/' . $path . '?v=' . date('YmdH');
}

function makeDropdown($options = [], $selected = [], $keyValue = 1)
{
    if (! is_array($selected)) {
        $selected = is_numeric($selected) ? (int) $selected : $selected;
    }
    $return = [];
    if (count($options)) {
        foreach ($options as $k => $v) {
            if (is_array($v)) {
                $return[] = '<optgroup label="' . $k . '">';
                $return[] = makeDropdown($v, $selected);
                $return[] = '</optgroup>';
            } else {
                $value = $keyValue ? $k : $v;
                $sel   = '';
                if (is_array($selected)) {
                    if (in_array($value, $selected)) {
                        $sel = 'selected';
                    }
                } elseif ($selected === $value) {
                    $sel = 'selected';
                }
                $return[] = '<option value="' . $value . '" ' . $sel . '>' . trim(strip_tags($v)) . '</option>';
            }
        }
    }

    return implode('', $return);
}

function exMessage($e)
{
    return 'Error on line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage();
}

function respException($e, $data = [])
{
    return response()->json(array_merge_recursive(['message' => exMessage($e)], $data), 500);
}

function isDeveloperTaskId($id)
{
    return substr($id, 0, 3) == 'DT-' ? str_replace('DT-', '', $id) : 0;
}

function isRegularTaskId($id)
{
    return substr($id, 0, 2) == 'T-' ? str_replace('T-', '', $id) : 0;
}

function respJson($code, $message = '', $data = [])
{
    return response()->json(array_merge_recursive(['message' => $message], $data), $code);
}

function dailyHours($type = null)
{
    $data = [];
    for ($i = 0; $i < 24; $i++) {
        $temp        = pad0($i) . ':00:00';
        $data[$temp] = $temp;
    }

    return $data;
}

function reqValidate($data, $rules = [], $messages = [])
{
    $validator = Validator::make($data, $rules, $messages);

    return $validator->errors()->all();
}

function loginId()
{
    return \Auth::id() ?: 0;
}

function isAdmin()
{
    return auth()->user()->isAdmin();
}

function printNum($num)
{
    return number_format($num, 2, '.', ',');
}

function readFullFolders($dir, &$results = [])
{
    $files = scandir($dir);
    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (! is_dir($path)) {
            $results[] = $path;
        } elseif ($value != '.' && $value != '..') {
            readFullFolders($path, $results);
        }
    }

    return $results;
}

function readFolders($data)
{
    $return = [];
    foreach ($data as $key => $filePath) {
        $fileName = basename($filePath);
        $return[] = rtrim(str_replace($fileName, '', $filePath), '/');
    }
    $return = array_values(array_unique($return));
    sort($return);

    return $return;
}

function getCommunicationData($sdc, $sw)
{
    $site_dev            = \App\SiteDevelopment::where(['site_development_category_id' => $sdc->id, 'website_id' => $sw->id])->orderBy('id', 'DESC')->get()->pluck('id');
    $query               = \App\DeveloperTask::join('users', 'users.id', 'developer_tasks.assigned_to')->whereIn('site_developement_id', $site_dev)->where('status', '!=', 'Done')->select('developer_tasks.id', 'developer_tasks.task as subject', 'developer_tasks.status', 'users.name as assigned_to_name');
    $query               = $query->addSelect(DB::raw("'Devtask' as task_type,'developer_task' as message_type"));
    $taskStatistics      = $query->orderBy('developer_tasks.id', 'DESC')->get();
    $query1              = \App\Task::join('users', 'users.id', 'tasks.assign_to')->whereIn('site_developement_id', $site_dev)->whereNull('is_completed')->select('tasks.id', 'tasks.task_subject as subject', 'tasks.assign_status', 'users.name as assigned_to_name');
    $query1              = $query1->addSelect(DB::raw("'Othertask' as task_type,'task' as message_type"));
    $othertaskStatistics = $query1->orderBy('tasks.id', 'DESC')->get();
    $merged              = $othertaskStatistics->merge($taskStatistics);

    return $merged;
}

function insertGoogleAdsLog($input)
{
    if (is_array($input)) {
        $input['user_id']         = auth()->id();
        $input['user_ip_address'] = request()->ip();

        \App\Models\GoogleAdsLog::create($input);
    }

    return true;
}

function getMediaUrl($media)
{
    if ($media->disk == 's3') {
        return $media->getTemporaryUrl(Carbon::now()->addMinutes(config('constants.temporary_url_expiry_time')));
    } else {
        return $media->getUrl();
    }
}

function checkCurrentUriIsEnableForEmailAlert($uri)
{
    $route = \App\Routes::where('url', 'LIKE', $uri)->where('email_alert', 1)->first();

    return $route !== null;
}
