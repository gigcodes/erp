<?php

namespace App\Hubstaff;

use Illuminate\Database\Eloquent\Model;

class HubstaffActivity extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'task_id',
        'starts_at',
        'tracked',
        'keyboard',
        'mouse',
        'overall',
    ];

    public static function getActivitiesForWeek($week, $year)
    {
        $result = getStartAndEndDate($week, $year);
        $start = $result['week_start'];
        $end = $result['week_end'];

        return self::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')
            ->where('starts_at', '>=', $start)
            ->where('starts_at', '<', $end)
            ->select(['hubstaff_activities.*', 'hubstaff_members.user_id as system_user_id'])
            ->get();
    }
}
