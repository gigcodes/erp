<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;

class UserAvaibility extends Model {
    protected $fillable = [
        'user_id',
        'from',
        'to',
        'status',
        'note',
        'date',
        'day',
        'minute',
        'start_time',
        'end_time',
        'launch_time'
    ];

    public static function workSlots() {
        return [
            '06_07' => '06-07',
            '07_08' => '07-08',
            '08_09' => '08-09',
            '09_10' => '09-10',
            '10_11' => '10-11',
            '11_12' => '11-12',
            '12_13' => '12-13',
            '13_14' => '13-14',
            '14_15' => '14-15',
            '15_16' => '15-16',
            '16_17' => '16-17',
            '17_18' => '17-18',
            '18_19' => '18-19',
            '19_20' => '19-20',
            '20_21' => '20-21',
        ];
    }
}
