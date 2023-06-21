<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Supplier;


class CodeShortcut extends Model
{
    public $table = 'code_shortcuts';

    protected $fillable = [
        'user_id',
        'supplier_id',
        'code',
        'description'
    ];

    public function user_detail()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function supplier_detail()
    {
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }
}
