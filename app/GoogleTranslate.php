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

class GoogleTranslate
{
    protected $path;

    function __construct() {
        $this->path = public_path().'/google/translation_key.json';

    }

    public function translate($target, $text) {

        // on production site it will return the original text
        if(env("IS_SITE","local") != "production") {
            return $text;
        }

        $translate = new TranslateClient([
            'keyFile' => json_decode(file_get_contents($this->path), true)
        ]);
        
        $result = $translate->translate($text, [
            'target' => $target
        ]);

        return $result['text'];
    }
}
