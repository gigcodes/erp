<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreSocialContentReview extends Model
{
    protected $fillable = ['file_id','review','review_by'];
}
