<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\UserEvent\UserEvent;
use Auth;
use Illuminate\Http\Request;

class UserEventController extends Controller
{

    function index()
    {
        return view('user-event.index');
    }

    /**
     * list of user events as json
     */
    function list(Request $request)
    {

        $userId = Auth::user()->id;

        if (!$userId) {
            return response()->json(
                [
                    'message' => 'Not allowed'
                ],
                401
            );
        }

        $start = explode('T', $request->get('start'))[0];
        $end = explode('T', $request->get('end'))[0];

        $events = UserEvent::with(['attendees'])
            ->where('start', '>=', $start)
            ->where('end', '<', $end)
            ->where('user_id', $userId)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'subject' => $event->subject,
                    'description' => $event->description,
                    'date' => $event->date,
                    'start' => $event->start,
                    'end' => $event->end,
                    'attendees' => $event->attendees
                ];
            });


        return response()->json($events);
    }

    /**
     * edit event
     */
    function editEvent(Request $request, int $id)
    {
        $userId = Auth::user()->id;

        if (!$userId) {
            return response()->json(
                [
                    'message' => 'Not allowed'
                ],
                401
            );
        }

        $start = $request->get('start');
        $end = $request->get('end');

        $userEvent = UserEvent::find($id);

        if (!$userEvent) {
            return response()->json(
                [
                    'message' => 'Event not found'
                ],
                404
            );
        }

        if ($userEvent->user_id != $userId) {
            return response()->json(
                [
                    'message' => 'Not allowed to edit event'
                ],
                401
            );
        }

        $userEvent->start = $start;
        $userEvent->end = $end;
        $userEvent->save();

        return response()->json([
            'message' => 'Event updated',
            'event' => [
                'id' =>  $userEvent->id,
                'title' =>  $userEvent->title,
                'start' =>  $userEvent->start,
                'end' => $userEvent->end
            ]
        ]);
    }

    /**
     * Create a new event
     */
    function createEvent(Request $request)
    {
        $userId = Auth::user()->id;

        if (!$userId) {
            return response()->json(
                [
                    'message' => 'Not allowed'
                ],
                401
            );
        }



        $date = $request->get('date');
        $time = $request->get('time');
        $subject = $request->get('subject');
        $description = $request->get('description');
        $contactsString = $request->get('contacts');

        $errors = array();

        // date validations
        if (!$date) {
            $errors['date'][] = 'Date is missing';
        } else if (!preg_match('/^[0-9]{4}-((0[1-9])|(1[0|1|2]))-(0|1|2|3)[0-9]$/', $date)) {
            $errors['date'][] = 'Invalid date format';
        } else if (!validateDate($date)) {
            $errors['date'][] = 'Invalid date';
        }

        if (isset($time)) {
            if (!preg_match('/^(([0-1][0-9])|(2[0-3])):[0-5][0-9]$/', $time)) {
                $errors['time'] = 'Invalid time format';
            }
        }

        if (empty(trim($subject))) {
            $errors['subject'][] = 'Subject is required';
        }

        if (!empty($errors)) {
            return response()->json($errors, 400);
        }


        $start = $date . ' ' . $time;
        $end = strtotime($start . ' + 1 hour');
        $start = strtotime($start);


        $userEvent = new UserEvent;
        $userEvent->user_id = $userId;
        $userEvent->subject = $subject;
        $userEvent->description = $description;
        $userEvent->date = $date;

        if (isset($time)) {
            $start = strtotime($date . ' ' . $time);
            $end = strtotime($date . ' ' . $time . ' + 1 hour');
            $userEvent->start = date('Y-m-d H:i:s', $start);
            $userEvent->end = date('Y-m-d H:i:s', $end);
        }



        $userEvent->save();

        return response()->json([
            'message' => 'Event added successfully',
            'event' => $userEvent->toArray(),
            'attendees' => []
        ]);
    }

    function removeEvent(Request $request, $id)
    {
        $userId = Auth::user()->id;

        if (!$userId) {
            return response()->json(
                [
                    'message' => 'Not allowed'
                ],
                401
            );
        }

        $result = UserEvent::where('id', $id)->where('user_id', $userId)->delete();
        if ($result == 1) {
            return response()->json([
                'message' => 'Event deleted:' . $result
            ]);
        }

        return response()->json([
            'message' => 'Failed to deleted',
            404
        ]);
    }
}
