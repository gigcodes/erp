<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use App\Models\SopHasCategory;
use Illuminate\Database\Eloquent\Model;

class Sop extends Model
{
    protected $table = 'sops';

    protected $fillable = ['name', 'content', 'user_id'];

    protected $appends = ['selected_category_ids'];

    public function purchaseProductOrderLogs()
    {
        return $this->hasOne(PurchaseProductOrderLog::class, 'purchase_product_order_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function emails()
    {
        return $this->hasMany(\App\Email::class, 'model_id', 'id');
    }

    public function sopCategory()
    {
        return $this->belongsToMany(SopCategory::class, "sop_has_categories", "sop_id", "sop_category_id");
    }

    public function hasSopCategory()
    {
        return $this->hasMany(SopHasCategory::class, "sop_id", "id");
    }

    /**
     * Model accrssor and mutator
     */
    public function getSelectedCategoryIdsAttribute()
    {
        return $this->sopCategory()->pluck('sops_category.id')->toArray();
    }
}
