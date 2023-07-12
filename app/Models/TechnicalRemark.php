<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicalRemark extends Model
{
    use HasFactory;

    public $table = 'technical_debt_remarks';

    protected $fillable = [
        'technical_debt_id',
        'remark',
        'updated_by',
    ];
}
