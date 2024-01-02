<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UicheckUserAccess extends Model
{
    protected $fillable = ['user_id', 'uicheck_id', 'lock_developer', 'lock_admin'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function provideAccess($uicheck_id, $user_id)
    {
        try {
            UicheckUserAccess::updateOrCreate([
                'user_id' => $user_id,
                'uicheck_id' => $uicheck_id,
            ], []);
        } catch (\Exception $e) {
        }
    }
}
