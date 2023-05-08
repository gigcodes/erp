<?php

namespace App\Observers;

use App\CallBusyMessage;

class CallBusyMessageObserver
{
    /**
     * Handle the call busy message "created" event.
     *
     * @return void
     */
    public function created(CallBusyMessage $callBusyMessage)
    {
        if ($callBusyMessage->recording_url != '') {
            $recordedText = (new CallBusyMessage)->convertSpeechToText($callBusyMessage->recording_url, 0, null, $callBusyMessage->twilio_call_sid);
            if ($recordedText != '') {
                CallBusyMessage::where('id', $callBusyMessage->id)->update(['audio_text' => $recordedText]);
            }
        }
    }

    /**
     * Handle the call busy message "updated" event.
     *
     * @return void
     */
    public function updated(CallBusyMessage $callBusyMessage)
    {
        if ($callBusyMessage->recording_url != '' and $callBusyMessage->isDirty('recording_url')) {
            $recordedText = (new CallBusyMessage)->convertSpeechToText($callBusyMessage->recording_url, 0, null, $callBusyMessage->twilio_call_sid);
            if ($recordedText != '') {
                CallBusyMessage::where('id', $callBusyMessage->id)->update(['audio_text' => $recordedText]);
            }
        }
    }

    /**
     * Handle the call busy message "deleted" event.
     *
     * @return void
     */
    public function deleted(CallBusyMessage $callBusyMessage)
    {
        //
    }

    /**
     * Handle the call busy message "restored" event.
     *
     * @return void
     */
    public function restored(CallBusyMessage $callBusyMessage)
    {
        //
    }

    /**
     * Handle the call busy message "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(CallBusyMessage $callBusyMessage)
    {
        //
    }
}
