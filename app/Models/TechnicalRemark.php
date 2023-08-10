<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TechnicalRemark extends Model
{
    use HasFactory;

    public $table = 'technical_debt_remarks';

    protected $fillable = [
        'technical_debt_id',
        'remark',
        'updated_by',
    ];

    public function users()
    {
        return $this->hasOne(\App\User::class, 'id', 'updated_by');
    }
}
