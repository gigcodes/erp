<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailRunHistories extends Model
{
    protected $table = 'email_run_histories';
    protected $fillable = [
        'email_address_id', 'is_success','message'
    ];
}
