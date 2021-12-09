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
use App\Loggers\TranslateLog;

class GoogleTranslate
{
    protected $path;

    function __construct() {
        $this->path = public_path().'/google/translation_key.json';

    }

    public function translate($target, $text) {
        $lastFileId = '';
        someLine:
        try {
            $file = googleTraslationSettings::select('id','account_json')
            ->where('status','1')
            ->orderBy('id')
            ->first();

            // on production site it will return the original text
            // if(env("IS_SITE","local") != "production") {
            //     return $text;
            // }
            if (!empty($file)) {
                $jsonArray = (array)json_decode($file->account_json);
                $lastFileId = $file->id;
                $keyFileArray = [
                    'keyFile' => $jsonArray
                ];

                $translate = new TranslateClient($keyFileArray);
            }else{
                $translate = new TranslateClient([
                    'keyFile' => json_decode(file_get_contents($this->path), true)
                ]);
            }
            // echo $target." ".$text;
            $result = $translate->translate($text, [
                'target' => $target
            ]);
            \Log::info(print_r(["Result of google",$result],true));
            return $result['text'];
        } catch (\Google\Cloud\Core\Exception\ServiceException $e) {
           
            // \Log::info("-----------------");
            // \Log::info(json_decode($e));
            // \Log::info($e->getServiceException());
           \Log::error($e);
            $message = json_decode($e->getMessage());
           // dd($message->error);

            if($message->error){
                $translateLog = TranslateLog::log([
                    "google_traslation_settings_id" => (!empty($lastFileId))?$lastFileId:0, 
                    "messages" =>$message->error->message,
                    "code" =>$message->error->code,
                    "domain" =>$message->error->errors[0]->domain,
                    "reason" =>$message->error->errors[0]->reason
                ]);
            // $translateLog = TranslateLog::log(["google_traslation_settings_id" => (!empty($lastFileId)), "messages" => $flow["name"] . " has found total Action  : " . $flowActions->count()]);
                
            }
            if (!empty($lastFileId)) {
                $googleTraslationSettings = new googleTraslationSettings;
                $googleTraslationSettings->where('id', $lastFileId)
                ->limit(1)
                ->update([
                    'status' => 0,
                ]);
                goto someLine;
            }
        }
    }
}
