<?php

namespace App;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

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
