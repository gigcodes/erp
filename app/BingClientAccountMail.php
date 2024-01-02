<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BingClientAccountMail extends Model
{
    protected $guarded = [];

    public function bing_client_account()
    {
        return $this->hasOne(BingClientAccount::class, 'id', 'bing_client_account_id');
    }
}
