<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class UserRate extends Model
{
    protected $fillable = [
        'user_id', 'start_date'
    ];

    static function getRateForUser($userId){
        return self::orderBy('start_date', 'desc')->where('user_id', $userId)->take(1)->first();
    }
}