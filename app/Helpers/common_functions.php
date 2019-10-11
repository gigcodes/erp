<?php

function printStatusView()
{

}

/**
 * Create image and text
 *
 *
 */

function createProductTextImage($path,$uploadPath = "", $text = "", $color = "545b62", $fontSize = "40" , $needAbs = true)
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

    if (!file_exists(public_path('uploads'.DIRECTORY_SEPARATOR.$uploadPath.DIRECTORY_SEPARATOR))) {
        mkdir(public_path('uploads'.DIRECTORY_SEPARATOR.$uploadPath.DIRECTORY_SEPARATOR), 0666, true);
    }

    $path = 'uploads'.DIRECTORY_SEPARATOR.$uploadPath.DIRECTORY_SEPARATOR. $name . '.jpg';

    $img->save(public_path($path));

    return ($needAbs) ? public_path($path) : url('/') . "/" . $path;
}
