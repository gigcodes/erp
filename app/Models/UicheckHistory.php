<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UicheckHistory extends Model
{
    public $table = 'uichecks_hisotry';

    public $fillable = [
        'uichecks_id',
        'type',
        'old_val',
        'new_val',
        'user_id',
    ];

    public function updatedBy()
    {
        return $this->hasOne(\App\User::class, 'id', 'user_id');
    }

    public function updatedByName()
    {
        return $this->updatedBy ? $this->updatedBy->name : '';
    }
}
