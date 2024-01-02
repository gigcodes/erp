<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class Translations extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="text",type="string")
     * @SWG\Property(property="text_original",type="string")
     * @SWG\Property(property="from",type="string")
     * @SWG\Property(property="to",type="string")
     * @SWG\Property(property="created_at",type="datetime")
     * @SWG\Property(property="updated_at",type="datetime")
     */
    /**
     * Fillables for the database
     *
     *
     * @var array
     */
    protected $fillable = [
        'text',
        'text_original',
        'from',
        'to',
    ];

    /**
     * Protected Date
     *
     * @var    array
     */
    /**
     * This static method will create new translation
     *
     * @param  string  $textOriginal
     * @param  string  $text
     * @param  string  $from
     * @param  string  $to
     * @return bool
     */
    public static function addTranslation($textOriginal, $text, $from, $to)
    {
        $obj = new Translations();
        $obj->text_original = $textOriginal;
        $obj->text = $text;
        $obj->from = $from;
        $obj->to = $to;

        $obj->save();
    }
}
