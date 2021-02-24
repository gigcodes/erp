<?php
/**
 * Created by PhpStorm.
 * User: mustafaflexwala
 * Date: 13/10/18
 * Time: 5:52 PM
 */

namespace App;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */


use Google\Cloud\Translate\V2\TranslateClient;
use App\googleTraslationSettings;

class GoogleTranslate
{
    protected $path;

    function __construct() {
        $this->path = public_path().'/google/translation_key.json';

    }

    public function translate($target, $text) {
        $file = googleTraslationSettings::select('account_json')
        ->where('status','1')
        ->orderBy('id')
        ->first();

        // on production site it will return the original text
        if(env("IS_SITE","local") != "production") {
            return $text;
        }

        if (!empty($file)) {
            $translate = new TranslateClient([
                'keyFile' => json_decode($file->account_json)
            ]);
        }else{
            $translate = new TranslateClient([
                'keyFile' => json_decode(file_get_contents($this->path), true)
            ]);
        }
        
        $result = $translate->translate($text, [
            'target' => $target
        ]);

        return $result['text'];
    }
}
