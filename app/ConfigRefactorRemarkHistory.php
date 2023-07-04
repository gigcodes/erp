<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use App\ConfigRefactor;
use Illuminate\Database\Eloquent\Model;

class ConfigRefactorRemarkHistory extends Model
{
    public $fillable = [
        'config_refactor_id',
        'column_name',
        'remarks',
        'user_id',
    ];

    public function configRefactor()
    {
        return $this->belongsTo(ConfigRefactor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}