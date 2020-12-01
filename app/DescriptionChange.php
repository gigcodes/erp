<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DescriptionChange extends Model
{
    protected $fillable = [
        'keyword',
        'replace_with',
    ];

    public static function getErpName($name)
    {
        $mc = self::all();
        $text = $name;
        foreach ($mc as $replace) {
            if(strpos($name,$replace->keyword) !== false){
                $text = str_replace(strtolower($replace->keyword), strtolower($replace->replace_with), strtolower($name));
            }
            # code...
        }
        return ucwords($text);
    }
}
