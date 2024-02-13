<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DevOppsCategories extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function subcategory()
    {
        return $this->hasMany(DevOppsSubCategory::class, 'devoops_category_id', 'id', 'status_id');
    }
}
