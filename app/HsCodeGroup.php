<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SimplyDutyCategory;
use App\HsCodeGroupsCategoriesComposition;

class HsCodeGroup extends Model
{
    public function hsCode()
    {
    	return $this->hasOne(SimplyDutyCategory::class,'code','hs_code_id');
    }

    public function groupComposition()
    {
    	return $this->hasMany(HsCodeGroupsCategoriesComposition::class,'hs_code_group_id','id');
    }


}
