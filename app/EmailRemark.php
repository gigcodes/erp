<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailRemark extends Model
{

  protected $fillable = [
    'email_id',
    'user_name',
    'remarks'
  ];

  public function email() {
    return $this->belongsTo(Email::class);
  }

}
