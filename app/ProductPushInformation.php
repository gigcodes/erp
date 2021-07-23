<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ProductPushInformation extends Model
{
    protected  $guarded = [];

    use SoftDeletes;

    public static function boot()
    {
        static::updated(function (ProductPushInformation $p) {

            $dirties = $p->getDirty();
            $old_contents = $p->getOriginal();
            $user_id = Auth::id();

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
            $new_values['user_id'] =$user_id ;
            ProductPushInformationHistory::create($new_values);
        });


        static::creating(function (ProductPushInformation $p) {

            $old_arr = [];
            $user_id = Auth::id();
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
                $old_arr['user_id'] = $user_id;
            }
            ProductPushInformationHistory::create($old_arr);
        });

    }
}
