<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Compositions extends Model
{
    //
    protected $fillable = [
        'name',
        'replace_with',
    ];

    public static function getErpName($name)
    {
        $mc = self::where("name", "like", "%" . $name . "%")->distinct('name')->get(['name', 'replace_with']);


        if (!$mc->isEmpty() && !empty($name)) {
            foreach ($mc as $key => $c) {
                if (stristr($name, $c->name)) {
                    return $c->replace_with;
                }
            }
        }

        // in this case color refenrece we don't found so we need to add that one
        if(!empty($name)) {
            self::create([
                'name'         => $name,
                'replace_with' => '',
            ]);
        }

        // Return an empty string by default
        return '';
    }
}
