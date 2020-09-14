<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Translations extends Model 
{
    /**
     * Fillables for the database
     *
     * @access protected
     *
     * @var array $fillable
     */
    protected $fillable = [
        'text',
        'text_original',
        'from',
        'to'
    ];

    /**
     * Protected Date
     *
     * @access protected
     * @var    array $dates
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * This static method will create new translation
     *
     * @param String $textOriginal
     * @param String $text
     * @param String $from
     * @param String $to
     *
     * @return bool 
     */
    public static function addTranslation($textOriginal, $text, $from, $to) {
        $obj = new Translations();
        $obj->text_original = $textOriginal;
        $obj->text = $text;
        $obj->from = $from;
        $obj->to = $to;

        $obj->save();
    }
}