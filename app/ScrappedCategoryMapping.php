<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScrappedCategoryMapping extends Model
{
    //
    protected $fillable = [
        'name'
    ];

    
    public function scmSPCM()
    {
        return $this->belongsToMany(ScrapedProducts::class, 'scrapped_product_category_mappings', 'category_mapping_id', 'product_id', 'id', 'id');
    }

    public function cat_count()
    {
        return $this->belongsToMany(ScrapedProducts::class, 'scrapped_product_category_mappings', 'category_mapping_id', 'product_id', 'id', 'id');
    }
}
