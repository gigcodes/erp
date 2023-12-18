<?php

namespace App\Models;
use App\Models\DevOppsCategories;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevOppsSubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'devoops_category_id', 'name',
    ];

    public function devoops_category()
    {
        return $this->belongsTo(DevOppsCategories::class);
    }
}
