<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Event;
use App\EventAvailability;
use Illuminate\Support\Str;

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
        $daterange = $request->get('daterange');
        $durationInMin = $request->get('duration_in_min');

        $errors = [];
        if (empty(trim($name))) {
            $errors['name'][] = 'Name is required';
        }

        if (empty(trim($description))) {
            $errors['description'][] = 'Description is required';
        }

        if (!$daterange) {
            $errors['daterange'][] = 'Date is missing';
        }

        if (empty(trim($durationInMin))) {
            $errors['duration_in_min'][] = 'Duration is required';
        }

        if (!empty($errors)) {
            return response()->json($errors, 400);
        }

        $date = explode('-', $daterange);
        $startDate = date('Y-m-d', strtotime($date[0]));
        $endDate = date('Y-m-d', strtotime($date[1]));

        // Event
        $event = new Event();
        $event->user_id = $userId;
        $event->name = $name;
        $event->slug = $slug;
        $event->description = $description;
        $event->start_date = $startDate;
        $event->end_date = $endDate;
        $event->duration_in_min = $durationInMin;
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
}
