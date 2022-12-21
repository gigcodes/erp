<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class CallBusyMessage extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="lead_id",type="integer")
     * @SWG\Property(property="twilio_call_sid",type="string")
     * @SWG\Property(property="caller_sid",type="string")
     * @SWG\Property(property="message",type="string")
     * @SWG\Property(property="recording_url",type="string")
     * @SWG\Property(property="status",type="string")
     * @SWG\Property(property="call_busy_messages",type="string")
     * @SWG\Property(property="created_at",type="datetime")
     * @SWG\Property(property="updated_at",type="datetime")
     */
    protected $fillable = ['lead_id', 'twilio_call_sid', 'caller_sid', 'message', 'recording_url', 'status', 'call_busy_message_statuses_id', 'audio_text'];

    protected $table = 'call_busy_messages';
    protected $casts = [
        'created_atcreated_at' => 'datetime',
    ];

    /**
     * Function to insert large amount of data
     *
     * @param  type  $data
     * @return $result
     */
    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            if ($model->twilio_call_sid) {
                $formatted_phone = str_replace('+', '', $model->twilio_call_sid);
                $customer = Customer::with('storeWebsite', 'orders')->where('phone', $formatted_phone)->first();
                $model->customer_id = $customer->id ?? null;
                $model->save();
            }
        });
    }

    public static function bulkInsert($data)
    {
        CallBusyMessage::insert($data);
    }

    /**
     * Function to check Twilio Sid
     *
     * @param  string  $sId twilio sid
     */
    public static function checkSidAlreadyExist($sId)
    {
        return CallBusyMessage::where('caller_sid', '=', $sId)->first();
    }

    public function status()
    {
        return $this->belongsTo(CallBusyMessageStatus::class, 'call_busy_message_statuses_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function convertSpeechToText($recording_url)
    {
        $recording_url = trim($recording_url);
        $ch1 = curl_init();
        curl_setopt($ch1, CURLOPT_URL, $recording_url);
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
        $http_respond = curl_exec($ch1);
        $http_respond = trim(strip_tags($http_respond));
        $http_code = curl_getinfo($ch1, CURLINFO_HTTP_CODE);
        curl_close($ch1);
        if (($http_code == '200') || ($http_code == '302')) {
            $apiKey = 'LhLBdFbWlwujC38ym7PcILaalqTBQN7Jb-50H0ij4nkG';
            $url = 'https://api.eu-gb.speech-to-text.watson.cloud.ibm.com/instances/9e2e85a8-4bea-4070-b3d3-cef36b5697f0';
            $ch = curl_init();
            $file = file_get_contents($recording_url);
            curl_setopt($ch, CURLOPT_URL, $url.'/v1/recognize');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $file);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_USERPWD, 'apikey'.':'.$apiKey);

            $headers = [];
            $headers[] = 'Content-Type: application/octet-stream';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:'.curl_error($ch);
            }
            curl_close($ch);
            $result = json_decode($result);

            return $recordedText = $result->results[0]->alternatives[0]->transcript;
        } else {
            return '';
        }
    }
}
