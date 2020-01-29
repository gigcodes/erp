<?php

function printStatusView()
{
}

/**
 * Create image and text
 *
 *
 */

function createProductTextImage($path, $uploadPath = "", $text = "", $color = "545b62", $fontSize = "40", $needAbs = true)
{
    $text = wordwrap(strtoupper($text), 24, "\n");

    $img  = \IImage::make($path);
    $img->resize(600, null, function ($constraint) {
        $constraint->aspectRatio();
    });
    // use callback to define details
    $img->text($text, 5, 50, function ($font) use ($fontSize, $color) {
        $font->file(public_path('/fonts/HelveticaNeue.ttf'));
        $font->size($fontSize);
        $font->color("#" . $color);
        $font->align('top');
    });

    $name = round(microtime(true) * 1000) . "_watermarked";

    if (!file_exists(public_path('uploads' . DIRECTORY_SEPARATOR . $uploadPath . DIRECTORY_SEPARATOR))) {
        mkdir(public_path('uploads' . DIRECTORY_SEPARATOR . $uploadPath . DIRECTORY_SEPARATOR), 0777, true);
    }

    $path = 'uploads' . DIRECTORY_SEPARATOR . $uploadPath . DIRECTORY_SEPARATOR . $name . '.jpg';

    $img->save(public_path($path));

    return ($needAbs) ? public_path($path) : url('/') . "/" . $path;
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
            previous_sibling($elements, $element["id"], $branch);
        }
    }

    return $branch;
}

/**
 * return all types of short message with postfix
 * 
 */

function show_short_message($message, $size = 50, $postfix = "...")
{
    $message = trim($message);

    $dot = "";

    if (strlen($message) > $size) {
        $dot = $postfix;
    }

    return substr($message, 0, $size) . $dot;
}

/**
 * key is using for to attach customer via session
 * 
 */

function attach_customer_key()
{
    return "customer_list_" . time() . "_" . auth()->user()->id;
}

/**
 *  get scraper last log file name
 */

function get_server_last_log_file($screaperName = "", $serverId = "")
{
    $d = date('d', strtotime("-1 days"));
    return "/scrap-logs/file-view/" . $screaperName . "-" . $d . ".log/" . $serverId;
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
