<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mediables extends Model
{
    public $table = "mediables";

    public static function getMediasFromProductId($product_id)
    {
       $columns = array('directory','filename','extension','disk','created_at');

       return  \App\Mediables::leftJoin("media as m",function($query){
                        $query->on("media_id","m.id");
                    })->where("mediable_id",$product_id)->where("mediable_type",\App\Product::class)->get($columns);
    }
}
