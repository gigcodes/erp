<?php
/**
 * Created by PhpStorm.
 * User: mustafaflexwala
 * Date: 17/08/18
 * Time: 9:57 PM
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Mediable\Mediable;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class Brand extends Model
{
    use SoftDeletes;
    use Mediable;

    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="euro_to_inr",type="string")
     * @SWG\Property(property="deduction_percentage",type="integer")
     * @SWG\Property(property="magento_id",type="integer")
     * @SWG\Property(property="brand_segment",type="string")
     * @SWG\Property(property="sku_strip_last",type="string")
     * @SWG\Property(property="sku_add",type="string")
     * @SWG\Property(property="references",type="string")
     * @SWG\Property(property="min_sale_price",type="integer")
     * @SWG\Property(property="max_sale_price",type="integer")
     */
    protected $fillable = ['name', 'euro_to_inr', 'deduction_percentage', 'magento_id', 'brand_segment', 'sku_strip_last', 'sku_add', 'sku_search_url', 'references', 'min_sale_price', 'max_sale_price', 'next_step'];

    /**
     * @var string
     *
     * @SWG\Property(property="deleted_at",type="datetime")
     */
    const BRAND_SEGMENT = [
        'A' => 'A',
        'B' => 'B',
        'C' => 'C',
    ];

    public static function getAll()
    {
        // Get all Brands
        $brands = self::all();

        // Create empty array to store brands
        $brandsArray = [];

        // Loop over brands
        foreach ($brands as $brand) {
            $brandsArray[$brand->id] = $brand->name;
        }

        // Sort array
        asort($brandsArray);

        // Return brands array
        return $brandsArray;
    }

    public static function getFormattedBrandName($brandName = '')
    {
        // Check for a brand name that matches
        switch ($brandName) {
            case 'ALEXANDER McQUEEN':
                $brandName = 'ALEXANDER Mc QUEEN';
                break;
            case 'TODS':
                $brandName = 'TOD-S';
                break;
            case 'Yves Saint Laurent':
                $brandName = 'saint-laurent';
                break;
            case 'DOLCE & GABBANA':
                $brandName = 'dolce-gabbana';
                break;
        }

        // Standard replaces
        $brandName = str_replace(' &amp; ', ' ', $brandName);
        $brandName = str_replace('&amp;', '', $brandName);

        // Return brand name
        return $brandName;
    }

    public function scrapedProducts()
    {
        return $this->hasMany(ScrapedProducts::class, 'brand_id', 'id');
    }

    public function dev_tasks()
    {
        return $this->hasMany(DeveloperTask::class, 'scraper_id', 'id');
    }

    public function brandTask()
    {
        return $this->hasMany(DeveloperTask::class, 'brand_id', 'id');
    }

    public function singleBrandTask()
    {
        return $this->hasOne(DeveloperTask::class, 'brand_id', 'id')->latest();
    }

    public function multiBrandTask($brandId, $devCheckboxs)
    {
        return \App\DeveloperTask::where('brand_id', $brandId)->whereIn('assigned_to', $devCheckboxs)->first();
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'brand', 'id');
    }

    public function supplierbrandcount()
    {
        return $this->hasOne(SupplierBrandCount::class, 'brand_id', 'id');
    }

    public function googleServer()
    {
        return $this->hasOne(GoogleServer::class, 'id', 'google_server_id');
    }

    public function skuFormat()
    {
        return $this->hasOne(SkuFormat::class, 'brand_id', 'id');
    }

    public static function getBrands()
    {
        return self::where('magento_id', '>', 0)->get();
    }

    public static function getSegmentPrice($brandId, $categoryId)
    {
        return \App\BrandCategoryPriceRange::where('brand_segment', $brandId)->where('category_id', $categoryId)->first();
    }

    public static function list()
    {
        return self::pluck('name', 'id')->toArray();
    }

    public function storewebbrand()
    {
        return $this->hasOne(StoreWebsiteBrand::class, 'brand_id', 'id');
    }

    public function storewebsitebrand($StoreID)
    {
        $record = $this->hasOne(StoreWebsiteBrand::class, 'brand_id', 'id')->where('store_website_id', $StoreID)->first();
        if ($record) {
            return $record->magento_value ?? '';
        } else {
            return '';
        }
    }

    public function productCountInExternalScraper()
    {
        return \App\Product::where('brand', $this->id)->where('status', \App\Helpers\StatusHelper::$requestForExternalScraper)->count();
    }

    public function productFromExternalScraper()
    {
        return \App\ScrapedProducts::where('brand_id', $this->id)->where('is_external_scraper', '>', 0)->count();
    }

    public static function searchBrand1($keyWord)
    {
        // Get all Brands
        $brands = self::where('name', 'LIKE', '%' . strtolower($keyWord) . '%');

        // Create empty array to store brands
        $brandsArray = [];

        // Loop over brands
        foreach ($brands as $brand) {
            $brandsArray[$brand->id] = $brand->name;
        }

        // Sort array
        asort($brandsArray);

        // Return brands array
        return $brandsArray;
    }

    public static function updateStatusIsHashtagsGenerated($brand_id_list)
    {
        Brand::whereIn('id', $brand_id_list)->where('is_hashtag_generated', 0)->update(['is_hashtag_generated' => 1]);
    }
}
