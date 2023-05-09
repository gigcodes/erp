<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PinterestBusinessAccounts extends Model
{
    protected $fillable = ['pinterest_client_id', 'pinterest_client_secret', 'pinterest_application_name', 'is_active'];
}
