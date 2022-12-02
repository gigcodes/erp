<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetPlateForm extends Model
{
    protected $table = 'asset_plate_forms';

    protected $fillable = [
        'id',
        'name',
    ];
}
