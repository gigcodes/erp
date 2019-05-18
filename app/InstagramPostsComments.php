<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstagramPostsComments extends Model
{
    public function nationality() {
        return $this->belongsTo(PeopleNames::class, 'people_id', 'id');
    }
}
