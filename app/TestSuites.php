<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestSuites extends Model
{
    protected $guarded = ['id'];

    public function module()
    {
        $this->belongsTo(SiteDevelopmentCategory::class, 'module_id', 'id');
    }

    public function whatsappAll($needBroadcast = false)
    {
        if ($needBroadcast) {
            return $this->hasMany(\App\ChatMessage::class, 'test_suites_id')->where(function ($q) {
                $q->whereIn('status', ['7', '8', '9', '10'])->orWhere('group_id', '>', 0);
            })->latest();
        } else {
            return $this->hasMany(\App\ChatMessage::class, 'test_suites_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
        }
    }
}
