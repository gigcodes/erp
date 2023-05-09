<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PasswordRemark extends Model
{
    use HasFactory;

    protected $fillable = ['password_id', 'password_type', 'updated_by', 'remark', 'create_at', 'updated_at'];

    protected $table = 'password_remark';

    public function users()
    {
        return $this->hasOne(\App\User::class, 'id', 'updated_by');
    }
}
