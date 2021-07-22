<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPushInformation extends Model
{
    protected  $guarded = [];

    use SoftDeletes;

    public static function boot()
    {
        static::updated(function (ProductPushInformation $p) {

            $dirties = $p->getDirty();
            $old_contents = $p->getOriginal();

            $old_arr = [];
            $remove_key = ['deleted_at', 'created_at', 'updated_at', 'id'];
            foreach ($old_contents as $key => $oldValue) {
                if (in_array($key, $remove_key)) {
                    continue;
                }
                if ($key === 'product_id') {
                    $old_arr['product_id'] = $oldValue;
                    continue;
                }

                $old_arr['old_' . $key] = $oldValue;
            }

            $new_values =  array_merge($old_arr, $dirties);
            ProductPushInformationHistory::create($new_values);
        });


        static::creating(function (ProductPushInformation $p) {

            $old_arr = [];
            $remove_key = ['deleted_at', 'created_at', 'updated_at', 'id'];
            foreach ($p->toArray() as $key => $oldValue) {
                if (in_array($key, $remove_key)) {
                    continue;
                }
                if ($key === 'product_id') {
                    $old_arr['product_id'] = $oldValue;
                    continue;
                }

                $old_arr['old_' . $key] = $oldValue;
            }
            ProductPushInformationHistory::create($old_arr);
        });

    }
}
