<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SitejabberQA extends Model
{

    protected $table = 'sitejabber_q_a_s';

    public function answers() {
        return $this->hasMany(__CLASS__, 'parent_id', 'id');
    }
}
