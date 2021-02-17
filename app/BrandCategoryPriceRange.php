<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class BrandCategoryPriceRange extends Model
{
	/**
     * @var string
     * @SWG\Property(enum={"brand_category_price_range"})
     */
    protected $table = 'brand_category_price_range';
    /**
     * @var string
     * @SWG\Property(enum={"brand_segment", "category_id", "min_price", "max_price"})
     */
    protected $fillable = ['brand_segment', 'category_id', 'min_price', 'max_price'];
}
