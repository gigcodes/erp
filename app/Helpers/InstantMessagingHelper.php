<?php

namespace App\Helpers;

use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use App\ChatMessage;
use App\Customer;
use App\ImQueue;

class InstantMessagingHelper
{

    /**
     * Save Messages For Send Whats App
     *
     * @param $numberTo , $text , $image , $priority, $numberFrom , $client , sendAfter
     * @return void
     * @static
     */
    public static function sendInstantMessage($numberTo, $text = null, $image = null, $priority = null, $numberFrom = null, $client = null, $sendAfter = null)
    {
        // Check for image and text
        if ($image != null || $text != null) {
            // Check if there is a number
            if ($numberTo == '' || $numberTo == null) {
                return redirect()->back()->withErrors('Please provide a number to send the message to');
            }

            //default number for send message
            if ($numberFrom == null) {
                $numberFrom = env('DEFAULT_SEND_NUMBER');
            }

            //setting default client name
            if ($client == null) {
                $client = 'whatsapp';
            }

            //saving queue
            $queue = new ImQueue();
            $queue->im_client = $client;
            $queue->number_to = $numberTo;
            $queue->number_from = $numberFrom;

            //getting image or text
            if ($image != null && $text != null) {
                $queue->image = self::encodeImage($text, $image);
            } elseif ($image != null) {
                $queue->image = self::encodeImage('', $image);
            } else {
                $queue->text = $text;
            }

            //setting priority
            if ($priority == null) {
                $queue->priority = 10;
            } else {
                $queue->priority = $priority;
            }

            //setting send after
            $queue->send_after = $sendAfter;
            $queue->save();

            //returning response
            return redirect()->back()->withSuccess('Mesage Saved');
        } else {
            //returning error in response
            return redirect()->back()->withErrors('Please Provide with image link or message');
        }
    }

    public static function scheduleMessage($numberTo, $numberFrom, $message = null, $image = null, $priority = 1, $sendAfter = null)
    {
        // Check last message to this number - TODO: This works for now, but not once we start scheduling messages from the system
        $maxTime = ImQueue::select(DB::raw('IF(MAX(send_after)>MAX(sent_at), MAX(send_after), MAX(sent_at)) AS maxTime'))->first();

        // Convert maxTime to unixtime
        $maxTime = strtotime($maxTime->maxTime);

        // Add interval
        $maxTime = $maxTime + 300;

        // Check if it's in the future
        if ($maxTime < time()) {
            $maxTime = time();
        }

        // Check for decent times
        if (date('H', $maxTime) < 8) {
            $sendAfter = date('Y-m-d 08:00:00', $maxTime);
        } elseif (date('H', $maxTime) > 18) {
            $sendAfter = date('Y-m-d 08:00:00', $maxTime + 86400);
        } else {
            $sendAfter = date('Y-m-d H:i:s', $maxTime);
        }

        // Insert message into queue
        $imQueue = new ImQueue();
        $imQueue->im_client = 'whatsapp';
        $imQueue->number_to = $numberTo;
        $imQueue->number_from = $numberFrom;
        $imQueue->text = $message;
        $imQueue->image = $image;
        $imQueue->priority = $priority;
        $imQueue->send_after = $sendAfter;
        return $imQueue->save();
    }


    /**
     * Return Json Encode URL
     *
     * @param $text , $image
     * @return jsonencoded image
     */
    public function encodeImage($text = null, $image)
    {
        // Get filename from image URL
        $filename = basename($image);

        // Get caption from text
        if ($text == null) {
            $image = array('body' => $image, 'filename' => $filename, 'caption' => '');
        } else {
            $image = array('body' => $image, 'filename' => $filename, 'caption' => $text);
        }

        // Return json encoded array
        return json_encode($image);
    }

    public static function replaceTags(Customer $customer, $message)
    {
        // Set tags to replace
        $fields = [
            '[[NAME]]' => $customer->name,
            '[[CITY]]' => $customer->city,
            '[[EMAIL]]' => $customer->email,
            '[[PHONE]]' => $customer->phone,
            '[[PINCODE]]' => $customer->pincode,
            '[[WHATSAPP_NUMBER]]' => $customer->whatsapp_number,
            '[[SHOESIZE]]' => $customer->shoe_size,
            '[[CLOTHINGSIZE]]' => $customer->clothing_size
        ];

        // Get replacement tags from message
        preg_match_all("/\[[^\]]*\]]/", $message, $matches);
        $values = $matches[ 0 ];

        // Replace all tags
        foreach ($values as $value) {
            if (isset($fields[ $value ])) {
                $message = str_replace($value, $fields[ $value ], $message);
            }
        }

        // Return message
        return $message;
    }
}