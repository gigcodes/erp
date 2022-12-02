<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BuildProcessHistory extends Model
{
    protected $table = 'build_process_histories';

    protected $fillable = ['id', 'store_website_id', 'status', 'text', 'build_name', 'build_number', 'created_by'];
}
