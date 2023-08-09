<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TechnicalFrameWork extends Model
{
    use HasFactory;

    public $table = 'technical_frameworks';

    protected $fillable = [
        'name',
    ];

    public function technical_depts()
    {
        return $this->belongsTo(TechnicalDebt::class);
    }
}
