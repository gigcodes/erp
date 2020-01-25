<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class UserRate extends Model
{
    protected $fillable = [
        'user_id', 'start_date'
    ];

    static function getRateForUser($userId){
        return self::orderBy('start_date', 'desc')->where('user_id', $userId)->take(1)->first();
    }

    /**
     * Carry forward the rates from last week to be a part of calculation
     */    
    public static function latestRatesForPreviousWeek(){

        $date = date('Y-m-d',strtotime('last sunday'));

        $query =  "SELECT
        *
      from user_rates
      where
        id in (
          SELECT
            GROUP_CONCAT(id) as id
          FROM (
              SELECT
                *
              FROM `user_rates`
              WHERE
                start_date < '$date'
            ) as a
          group by
            user_id
        )";

        $rateData = DB::select($query);

        return self::hydrate($rateData);

    }
}