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
