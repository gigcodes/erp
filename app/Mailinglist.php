<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mailinglist extends Model
{
    protected $fillable = ['name', 'remote_id', 'service_id'];

    public function service()
    {
       return $this->belongsTo(Service::class);
    }
}
