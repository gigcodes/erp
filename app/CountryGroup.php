<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CountryGroup extends Model
{

    protected $fillable = [
        'name',
        'created_at',
        'updated_at',
    ];

    public function groupItems()
    {
        return $this->hasMany("\App\CountryGroupItem","country_group_id","id");
    }

    public static function list()
    {
        return self::pluck("name","id")->toArray();
    }

}
