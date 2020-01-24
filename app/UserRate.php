<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class UserRate extends Model
{
    protected $fillable = [
        'user_id', 'start_date'
    ];
}