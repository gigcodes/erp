<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UicheckAttachement extends Model
{
    protected $table = 'uicheck_attachements';

    protected $fillable = ['user_id', 'uicheck_id', 'subject', 'description', 'filename'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function uicheck()
    {
        return $this->belongsTo(Uicheck::class);
    }
}
