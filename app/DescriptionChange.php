<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DescriptionChange extends Model
{
    protected $fillable = [
        'keyword',
        'replace_with',
    ];

    public static function getErpName($name)
    {
        $parts = preg_split('/\s+/', $name);
        
        $mc = self::query();
        if(!empty($parts))  {
            foreach($parts as $p){
                $mc->orWhere("keyword","like","%".trim($p)."%");
            }
        }
        $mc = $mc->distinct('keyword')->get(['keyword', 'replace_with']);

        $isReplacementFound = false;
        if (!$mc->isEmpty() && !empty($name)) {
            foreach ($mc as $key => $c) {
                // check if the full replacement found then assign from there
                if (strtolower($name) == strtolower($c->name) && !empty($c->replace_with)) {
                    return $c->replace_with;
                }

                foreach($parts as $p) {
                    if (strtolower($p) == strtolower($c->name) && !empty($c->replace_with)) {
                        $name = str_replace($p, $c->replace_with, $name);
                        $isReplacementFound = true;
                    }
                }
            }
        }

        // check if replacement found then assing that to the composition otherwise add new one and start next process
        if($isReplacementFound) {
            $checkExist = self::where('keyword','like',$name)->first();
            if($checkExist && !empty($checkExist->replace_with)) {
                return $checkExist->replace_with;
            }
        }

        // in this case color refenrece we don't found so we need to add that one
        if(!empty($name)) {
            $compositionModel = self::where('keyword',$name)->first();
            if(!$compositionModel) {
                self::create([
                    'keyword'         => $name,
                    'replace_with' => '',
                ]);
            }
        }

        // Return an empty string by default
        return $name;
    }
}
