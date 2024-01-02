<?php

namespace App;

use App\Models\PlanAction;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'plans';

    public function subList($id)
    {
        return $this->where('parent_id', $id)->get();
    }

    public function getPlanActionStrength()
    {
        return $this->hasMany(PlanAction::class, 'plan_id', 'id')
            ->where('plan_action_type', 1);
    }

    public function getPlanActionWeakness()
    {
        return $this->hasMany(PlanAction::class, 'plan_id', 'id')
            ->where('plan_action_type', 2);
    }

    public function getPlanActionOpportunity()
    {
        return $this->hasMany(PlanAction::class, 'plan_id', 'id')
            ->where('plan_action_type', 3);
    }

    public function getPlanActionThreat()
    {
        return $this->hasMany(PlanAction::class, 'plan_id', 'id')
            ->where('plan_action_type', 4);
    }
}
