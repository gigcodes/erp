<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CampaignEvent extends Model
{
    protected $fillable = [ 'id', 'email', 'event', 'date_event', 'created_at', 'updated_at'];
    protected $table = "campaign_events";
}
