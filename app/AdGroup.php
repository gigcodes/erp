<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class AdGroup extends Model
{
	/**
     * @var string
     * @SWG\Property(enum={"campaign_id", "type", "group_name", "url", "keywords","budget","google_campaign_id","google_ad_group_id","google_ad_group_response"})
     */
    protected $fillable = [
        'campaign_id', 'type', 'group_name', 'url', 'keywords','budget','google_campaign_id','google_ad_group_id','google_ad_group_response',
    ];
}
