<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostmanRequestCreate extends Model
{
    protected $table = 'postman_request_creates';

    protected $guarded = [];

    protected $casts = [
        'body_json' => 'array',
    ];

    public function latestRes()
    {
        return $this->hasOne(PostmanResponse::class, 'request_id', 'id');
    }

    public static function dropdownRequestNames()
    {
        return self::orderBy('request_name')->pluck('request_name', 'request_name')->toArray();
    }
}
