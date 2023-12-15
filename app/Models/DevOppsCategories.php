<?php

namespace App\Models;
use App\Models\DevOppsSubCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevOppsCategories extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function subcategory()
    {
        return $this->hasMany(DevOppsSubCategory::class, 'devoops_category_id', 'id');
    }
}
