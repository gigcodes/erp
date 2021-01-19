<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SimplyDutyCountry extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['country_code','country_name','default_duty'];

    public static function getSelectList()
    {
        return self::pluck("country_name","country_code")->toArray();
    }
}
