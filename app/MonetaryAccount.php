<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonetaryAccount extends Model
{
        protected $fillable = ['date','currency','amount','type','created_by','updated_by','short_note','description','other'];

}
