<?php

namespace App\Models\Seo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoCompanyType extends Model
{
    use HasFactory;

    protected $table = "seo_company_type";
    protected $fillable = [
        'name'
    ];
}
