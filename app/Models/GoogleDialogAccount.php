<?php

namespace App\Models;

use App\StoreWebsite;
use Illuminate\Database\Eloquent\Model;

class GoogleDialogAccount extends Model
{
    protected $fillable = [
        'google_client_id',
        'google_client_secret',
        'site_id'
    ];

    public function accounts()
    {
        return $this->hasMany(GoogleDialogAccountMails::class, 'google_dialog_account_id', 'id');
    }

    public function storeWebsite()
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'site_id');
    }
}
