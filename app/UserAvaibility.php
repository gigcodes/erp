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
    
    public static function availableSlots($stTime, $enTime) {
        $slots = [];
        $intrvl =  strtotime("1970-01-01 01:00:00 UTC");

        $dateTimes = new \DatePeriod(
            new \DateTime($stTime),
            new \DateInterval('PT' . $intrvl . 'S'),
            new \DateTime($enTime)
        );
        foreach ($dateTimes as $dt) {
            $h = $dt->format('H');
            $i = $dt->format('i');
            $slots[] = [
                'display' => $h.'-'.nextHour($h),
                'from' => $h.':'.$i,
                'to' => nextHour($h).':'.$i,
            ];
        }
        return $slots;
    }

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
