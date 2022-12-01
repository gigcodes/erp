<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class PlanAction extends Model
{
    public $table = 'plan_actions';

    public $fillable = [
        'plan_id',
        'plan_action',
        'plan_action_type',
        'created_by',
    ];

    public function getAdminUser()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
