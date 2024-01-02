<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoSettingValueHistory extends Model
{
    use HasFactory;

    protected $table = 'magento_setting_value_histories';

    protected $fillable = ['magento_setting_id', 'old_value', 'new_value',  'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
