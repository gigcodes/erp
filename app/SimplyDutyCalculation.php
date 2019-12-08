<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SimplyDutyCalculation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['hscode','value','duty','duty_rate','duty_hscode','duty_type','shipping','insurance','total',
    'exchange_rate','currency_type_origin','currency_type_destination','duty_minimis','vat_minimis','vat_rate','vat'];
}
