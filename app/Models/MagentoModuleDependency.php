<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class MagentoModuleDependency extends Model
{
    use HasFactory;

    protected $table = 'magento_module_dependancies';

    protected $fillable = ['user_id','magento_module_id', 'depency_remark',  'depency_module_issues', 'depency_theme_issues'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
