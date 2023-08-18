<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class VirtualminDomain extends Model
{
    protected $table = 'virtualmin_domains';

    protected $fillable = [
        'name',
        'is_enabled',
    ];

    protected $appends = ['is_enabled_text'];

    public function getIsEnabledTextAttribute()
    {
        $isEnabledText = '';
        if ($this->is_enabled === 1) {
            $isEnabledText = 'Enabled';        
        } else {
            $isEnabledText = 'Disabled';
        }
        return $isEnabledText;
    }
   

}
