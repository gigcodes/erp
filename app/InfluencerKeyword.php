<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InfluencerKeyword extends Model
{
    public function next(){
        // get next keyword
        return InfluencerKeyword::where('id', '>', $this->id)->orderBy('id','asc')->first();

    }
    public  function previous(){
        // get previous  keyword
        return InfluencerKeyword::where('id', '<', $this->id)->orderBy('id','desc')->first();

    }
}
