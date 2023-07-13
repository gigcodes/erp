<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TechnicalDebt;

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
