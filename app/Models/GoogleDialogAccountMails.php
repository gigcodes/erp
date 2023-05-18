<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleDialogAccountMails extends Model
{
    protected $guarded = [];

    protected $fillable = ['google_account', 'google_dialog_account_id', 'google_client_refresh_token', 'google_client_access_token', 'expires_in'];

    public function google_dialog_account()
    {
        return $this->hasOne(GoogleDialogAccount::class, 'id', 'google_dialog_account_id');
    }
}
