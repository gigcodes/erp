<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioCategory extends Model
{

    protected $table = 'twilio_categories';

    protected $fillable = ['category_name'];

}
