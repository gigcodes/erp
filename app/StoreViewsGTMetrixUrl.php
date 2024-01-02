<?php

namespace App;

use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class StoreViewsGTMetrixUrl extends Model
{
    use Mediable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'store_views_gt_metrix_url';

    protected $fillable = [
        'account_id',
        'store_view_id',
        'store_name',
        'website_url',
        'process',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'resources' => 'array',
    ];

    public function account()
    {
        return $this->belongsTo(StoreGTMetrixAccount::class, 'account_id');
    }
}
