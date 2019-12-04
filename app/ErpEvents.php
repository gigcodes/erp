<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ErpEvents extends Model
{
    protected $fillable = [
        "event_name",
        "event_description",
        "start_date",
        "end_date",
        "type",
        "brand_id",
        "category_id",
        "number_of_person",
        "product_start_date",
        "product_end_date",
        "minute",
        "hour",
        "day_of_month",
        "month",
        "day_of_week",
        "created_by"
    ];

}
