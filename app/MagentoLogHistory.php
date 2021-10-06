<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class MagentoLogHistory extends Model
{
   public $table = 'magento_log_history';

    protected $fillable = [
        'id',
        'log_id',
        'user_id',
        'old_value',
        'new_value',
        'created_at',
        'updated_at',
    ];
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
