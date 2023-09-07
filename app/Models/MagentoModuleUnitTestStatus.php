<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class MagentoModuleUnitTestStatus extends Model
{
    use HasFactory;

    public $table = 'magento_modules_unit_test_statuses';

    protected $fillable = ['unit_test_status_name'];

}
