<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MailingRemark extends Model
{
    protected $fillable = ['customer_id', 'user_id', 'text', 'user_name'];

}
