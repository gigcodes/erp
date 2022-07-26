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
    
    public static function getAvailableDates($days, $stDate, $enDate) {
        $data = [];
        $range = dateRangeArr($stDate, $enDate);
        foreach ($range as $value) {
            if (in_array($value['day'], $days)) {
                $data[] = $value['date'];
            }
        }
        return $data;
    }
}
