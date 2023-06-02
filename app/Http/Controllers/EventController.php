<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Event;
use App\EventAvailability;
use App\Mails\Manual\EventEmail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $events = Event::myEvents(Auth::user()->id)->latest()->paginate(25);

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('events.partials.index', compact('events'))->render(),
            ], 200);
        }

        return view('events.index', compact('events'));
    }

    public function store (Request $request) 
    {
        $userId = Auth::user()->id;

        if (!$userId) {
            return response()->json(
                [
                    'message' => 'Not allowed',
                ],
                401
            );
        }

        $name = $request->get('name');
        $slug = Str::slug($name);
        $description = $request->get('description');
        $durationInMin = $request->get('duration_in_min');
        $recurringEnd = $request->get('recurring_end');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $errors = [];
        if (empty(trim($name))) {
            $errors['name'][] = 'Name is required';
        }

        if (empty(trim($description))) {
            $errors['description'][] = 'Description is required';
        }

        if (empty(trim($startDate))) {
            $errors['start_date'][] = 'Start Date is required';
        }

        if (empty(trim($durationInMin))) {
            $errors['duration_in_min'][] = 'Duration is required';
        }

        $isRecurring = 0;
        if($request->has('is_recurring') && $request->get('is_recurring') == 'on') {
            $isRecurring = 1;
            if (empty(trim($recurringEnd))) {
                $errors['recurring_end'][] = 'Recurring end is required';
            }

            if ($recurringEnd == 'on' && empty(trim($endDate))) {
                $errors['end_date'][] = 'End date is required';
            }
            if ($recurringEnd == 'never') {
                $endDate = "";
            }
        } else {
            if (empty(trim($endDate))) {
                $errors['end_date'][] = 'End Date is required';
            }
        }

        if (!empty($errors)) {
            return response()->json($errors, 400);
        }

        // Event
        $event = new Event();
        $event->user_id = $userId;
        $event->name = $name;
        $event->slug = $slug;
        $event->description = $description;
        $event->start_date = $startDate;
        $event->end_date = $endDate;
        $event->duration_in_min = $durationInMin;
        $event->is_recurring = $isRecurring;
        $event->recurring_end = $recurringEnd;
        $event->save();

        // Event Availabilities 
        foreach ($request->event_availability as $key => $event_availability) {
            if (isset($event_availability["day"]) && $event_availability["day"] == "on") {
                $eventAvailability = new EventAvailability();
                $eventAvailability->event_id = $event->id;
                $eventAvailability->numeric_day = $key;
                $eventAvailability->start_at = date("H:i:s", strtotime($event_availability["start_at"]));
                $eventAvailability->end_at = date("H:i:s", strtotime($event_availability["end_at"]));
                $eventAvailability->save();
            }
        }

        return response()->json([
            'code' => 200,
            'message' => 'Event created successfully',
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $result = Event::where('id', $id)->myEvents(Auth::user()->id)->first();

        if ($result) {
            $result->delete();

            return response()->json([
                'code' => 200,
                'message' => 'Event deleted successfully !!',
            ]);
        }

        return response()->json([
            'message' => 'Failed to deleted',
            404,
        ]);
    }

    public function stopRecurring(Request $request, $id)
    {
        $result = Event::where('id', $id)->myEvents(Auth::user()->id)->first();
        if ($result) {
            $eventStartDate = Carbon::parse($result->start_date);
            $nowDate = Carbon::now();

            $result->recurring_end = "on";
            if ($nowDate->gt($eventStartDate)) {
                $result->end_date = date('Y-m-d');
            } else {
                $result->end_date = $result->start_date;
            }
            $result->save();

            return response()->json([
                'code' => 200,
                'message' => 'Recurring stopped successfully !!',
            ]);
        }

        return response()->json([
            'message' => 'Failed to stop recurring',
            404,
        ]);
    }

    public function reschedule(Request $request)
    {
        $userId = Auth::user()->id;

        if (!$userId) {
            return response()->json(
                [
                    'message' => 'Not allowed',
                ],
                401
            );
        }

        $event = Event::where('id', $request->get('id'))->myEvents(Auth::user()->id)->first();
        if(!$event) {
            return response()->json([
                'message' => 'Failed to reschedule',
                404,
            ]);
        }

        $durationInMin = $request->get('duration_in_min');
        $recurringEnd = $request->get('recurring_end');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $mailBody = $request->get('mail_body');

        $errors = [];
        if (empty(trim($startDate))) {
            $errors['start_date'][] = 'Start Date is required';
        }

        if (empty(trim($durationInMin))) {
            $errors['duration_in_min'][] = 'Duration is required';
        }

        $isRecurring = 0;
        if($request->has('is_recurring') && $request->get('is_recurring') == 'on') {
            $isRecurring = 1;
            if (empty(trim($recurringEnd))) {
                $errors['recurring_end'][] = 'Recurring end is required';
            }

            if ($recurringEnd == 'on' && empty(trim($endDate))) {
                $errors['end_date'][] = 'End date is required';
            }
            if ($recurringEnd == 'never') {
                $endDate = "";
            }
        } else {
            if (empty(trim($endDate))) {
                $errors['end_date'][] = 'End Date is required';
            }
        }

        if (empty(trim($mailBody))) {
            $errors['mail_body'][] = 'Mail body is required';
        }

        if (!empty($errors)) {
            return response()->json($errors, 400);
        }

        // Event
        $event->start_date = $startDate;
        $event->end_date = $endDate;
        $event->duration_in_min = $durationInMin;
        $event->is_recurring = $isRecurring;
        $event->recurring_end = $recurringEnd;
        $event->save();

        if ($event->eventSchedules->count() > 0) {
            foreach($event->eventSchedules as $eventSchedule) {
                $this->emailSend($event, $eventSchedule, $mailBody);
            }
        }

        return response()->json([
            'code' => 200,
            'message' => 'Event rescheduled successfully',
        ]);
    }

    public function emailSend($event, $eventSchedule, $message)
    {
        $subject = "Reschedule Subject";
        $emailClass = (new EventEmail($subject, $message, $event->user->email))->build();

        $email = \App\Email::create([
            'model_id' => $event->id,
            'model_type' => Event::class,
            'from' => $event->user->email,
            'to' => $eventSchedule->public_email,
            'subject' => $subject,
            'message' => $emailClass->render(),
            'template' => '',
            'additional_data' => '',
            'status' => 'pre-send',
            'store_website_id' => null,
        ]);

        \App\Jobs\SendEmail::dispatch($email)->onQueue('send_email');
    }
}
