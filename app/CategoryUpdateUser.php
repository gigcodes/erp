<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryUpdateUser extends Model
{
    public $fillable = [
        "supplier_id",
        "user_id"
    ];

    public function user()
    {
        return $this->hasOne(\App\User::class, "id","user_id");
    }

    public function supplier()
    {
        return $this->hasOne(\App\Supplier::class, "id", "supplier_id");
    }
}
