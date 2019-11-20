<?php

namespace App\Helpers;

use App\ImQueue;
use App\ChatMessage;
use Illuminate\Http\Request;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use DB;
use Dompdf\Dompdf;


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
        //check if image and text are not null
        if ($image != null || $text != null) {
            if ($numberTo == '' || $numberTo == null) {
                return redirect()->back()->withErrors('Please Provide To Send');
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


    /**
     * Return Json Encode URL
     *
     * @param $text , $image
     * @return jsonencoded image
     */
    public function encodeImage($text = null, $image)
    {
        //getting file name from image url
        $filename = basename($image);

        //getting caption from text
        if ($text == null) {
            $image = array('body' => $image, 'filename' => $filename, 'caption' => '');
        } else {
            $image = array('body' => $image, 'filename' => $filename, 'caption' => $text);
        }
        //returning result
        return json_encode($image);
    }

}