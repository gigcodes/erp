<?php

namespace App;

use App\Models\PlanAction;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'plans';

    function subList($id)
    {
    	return $this->where('parent_id',$id)->get();
    }
    function getPlanActionStrength() {
        return $this->hasMany(PlanAction::class,'plan_id','id')
            ->where('plan_action_type',1);
    }
    function getPlanActionWeakness() {
        return $this->hasMany(PlanAction::class,'plan_id','id')
            ->where('plan_action_type',2);
    }
    function getPlanActionOpportunity() {
        return $this->hasMany(PlanAction::class,'plan_id','id')
            ->where('plan_action_type',3);
    }
    function getPlanActionThreat() {
        return $this->hasMany(PlanAction::class,'plan_id','id')
            ->where('plan_action_type',4);
    }
}
