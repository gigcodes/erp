<?php

namespace App\Models;

use App\StoreWebsite;
use Illuminate\Database\Eloquent\Model;

class GoogleDialogAccount extends Model
{
    protected $fillable = [
        'service_file',
        'site_id',
        'project_id',
        'default_selected',
        'email',
    ];

    public function storeWebsite()
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'site_id');
    }
}
