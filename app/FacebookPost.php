<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Plank\Mediable\Mediable;
class FacebookPost extends Model
{
    use Mediable;

    protected $fillable = [
        'account_id',
        'caption',
        'post_body',
        'post_by',
        'posted_on',
        'status'
    ];
    public function account()
    {
        return $this->belongsTo('App\Account');
    }
}
