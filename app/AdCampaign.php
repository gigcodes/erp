<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class AdCampaign extends Model
{
	/**
     * @var string
     * @SWG\Property(enum={"goal", "type", "campaign_name", "data","campaign_budget_id","campaign_id","campaign_response"})
     */
    protected $fillable = [
        'goal', 'type', 'campaign_name', 'data','campaign_budget_id','campaign_id','campaign_response',
    ];
}
