<?php

namespace App;

use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use Mediable;

    protected $fillable = ['is_queue', 'unique_id', 'lead_id', 'order_id', 'customer_id', 'supplier_id', 'vendor_id', 'user_id', 'task_id', 'erp_user', 'contact_id', 'dubbizle_id', 'assigned_to', 'purchase_id', 'message', 'media_url', 'number', 'approved', 'status', 'error_status', 'resent', 'is_reminder', 'created_at', 'issue_id', 'developer_task_id', 'lawyer_id', 'case_id', 'blogger_id', 'voucher_id', 'document_id', 'group_id','old_id','message_application_id'];
    protected $table = "chat_messages";
    protected $dates = ['created_at', 'updated_at'];
    protected $casts = array(
        "approved" => "boolean"
    );

    /**
     * Send WhatsApp message via Chat-Api
     * @param $number
     * @param null $whatsAppNumber
     * @param null $message
     * @param null $file
     * @return bool|mixed
     */
    public static function sendWithChatApi($number, $whatsAppNumber = null, $message = null, $file = null)
    {
        // Get configs
        $config = \Config::get("apiwha.instances");

        // Set instanceId and token
        if (isset($config[ $whatsAppNumber ])) {
            $instanceId = $config[ $whatsAppNumber ][ 'instance_id' ];
            $token = $config[ $whatsAppNumber ][ 'token' ];
        } else {
            $instanceId = $config[ 0 ][ 'instance_id' ];
            $token = $config[ 0 ][ 'token' ];
        }

        // Add plus to number and add to array
        $chatApiArray = [
            'phone' => '+' . $number
        ];

        if ($message != null && $file == null) {
            $chatApiArray[ 'body' ] = $message;
            $link = 'sendMessage';
        } else {
            $exploded = explode('/', $file);
            $filename = end($exploded);
            $chatApiArray[ 'body' ] = $file;
            $chatApiArray[ 'filename' ] = $filename;
            $link = 'sendFile';
            $chatApiArray[ 'caption' ] = $message;
        }

        // Init cURL
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.chat-api.com/instance$instanceId/$link?token=" . $token,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($chatApiArray),
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json",
            ),
        ));

        // Get response
        $response = curl_exec($curl);

        // Get possible error
        $err = curl_error($curl);

        // Close cURL
        curl_close($curl);

        // Check for errors
        if ($err) {
            // Log error
            \Log::channel('whatsapp')->debug("(file " . __FILE__ . " line " . __LINE__ . ") cURL Error for number " . $number . ":" . $err);
            return false;
        } else {
            // Log curl response
            \Log::channel('chatapi')->debug('cUrl:' . $response . "\nMessage: " . $message . "\nFile:" . $file . "\n");

            // Json decode response into result
            $result = json_decode($response, true);

            // Check for possible incorrect response
            if (!is_array($result) || array_key_exists('sent', $result) && !$result[ 'sent' ]) {
                // Log error
                \Log::channel('whatsapp')->debug("(file " . __FILE__ . " line " . __LINE__ . ") Something was wrong with the message for number " . $number . ": " . $response);
                return false;
            } else {
                // Log successful send
                \Log::channel('whatsapp')->debug("(file " . __FILE__ . " line " . __LINE__ . ") Message was sent to number " . $number . ":" . $response);
            }
        }

        return $result;
    }

    /**
     * Handle Chat-Api ACK-message
     * @param $json
     */
    public static function handleChatApiAck($json)
    {
        // Loop over ack
        if (isset($json[ 'ack' ])) {
            foreach ($json[ 'ack' ] as $chatApiAck) {
                // Find message
                $chatMessage = self::where('unique_id', $chatApiAck[ 'id' ])->first();

                // Chat Message found and status is set
                if ($chatMessage && isset($chatApiAck[ 'status' ])) {
                    // Set delivered
                    if ($chatApiAck[ 'status' ] == 'delivered') {
                        $chatMessage->is_delivered = 1;
                        $chatMessage->save();
                    }

                    // Set views
                    if ($chatApiAck[ 'status' ] == 'viewed') {
                        $chatMessage->is_delivered = 1;
                        $chatMessage->is_read = 1;
                        $chatMessage->save();
                    }
                }
            }
        }
    }

    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }

    public function lawyer()
    {
        return $this->belongsTo('App\Lawyer');
    }

    /**
     * Check if the message has received a broadcast price reply
     * @return bool
     */
    public function isSentBroadcastPrice()
    {
        // Get count
        $count = $this->hasMany('App\CommunicationHistory', 'model_id')->where('model_type', 'App\ChatMessage')->where('type', 'broadcast-prices')->count();

        // Return true or false
        return $count > 0 ? true : false;
    }
}
