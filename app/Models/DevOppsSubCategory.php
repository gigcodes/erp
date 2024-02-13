<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DevOppsSubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'devoops_category_id', 'name',
    ];

    public function devoops_category()
    {
        return $this->belongsTo(DevOppsCategories::class, 'devoops_category_id');
    }

    public function status()
    {
        return $this->belongsTo(DevOopsStatus::class, 'status_id');
    }
}
