<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Charity extends Model
{
    //
	protected $fillable = ['name', 'contact_no', 'email', 'whatsapp_number', 'assign_to'];
}
