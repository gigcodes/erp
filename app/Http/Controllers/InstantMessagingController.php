<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ImQueue;
use \Carbon\Carbon;
use App\Helpers\InstantMessagingHelper;

class InstantMessagingController extends Controller
{
    /**
     * Send Message Queue Result For API Call
     *
     * @param $client
     * @param $numberFrom
     * @return void
     */
    public function getMessage($client, $numberFrom)
    {
        // Get next messsage from queue
        $queue = ImQueue::select('id', 'text', 'image', 'number_to')
            ->where('im_client', $client)
            ->where('number_from', $numberFrom)
            ->where(function ($query) {
                $query->where('send_after', '<', Carbon::now())
                    ->orWhereNull('send_after');
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->first();

        // Return error if no message is found
        if ($queue == null) {
            $message = ['error' => 'The queue is empty'];
            return json_encode($message, 400);
        }

        // Set output
        if ($queue->image != null) {
            $output = ['queueNumber' => $queue->id, 'phone' => $queue->number_to, 'body' => $queue->image, 'filename' => urlencode(substr($queue->image, strrpos($queue->image, '/') + 1)), 'caption' => $queue->text];
        } else {
            $output = ['queueNumber' => $queue->id, 'phone' => $queue->number_to, 'body' => $queue->text];
        }

        // Return output
        if (isset($output)) {
            return json_encode($output, 200);
        } else {
            return json_encode(['error' => 'The queue is empty'], 400);
        }
    }
}
