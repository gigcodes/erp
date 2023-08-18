<?php

namespace App\Http\Controllers;

use Auth;
use App\Event;
use Carbon\Carbon;
use App\AssetsManager;
use App\EventAvailability;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EventSchedule;
use App\Mails\Manual\EventEmail;
use Illuminate\Support\Collection;
use App\Models\EventCategory;

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
        return view(
            'events.calendar',
        );
    }

    public function publicEvents(Request $request)
    {
        $events = Event::myEvents(Auth::user()->id)->where('event_type', 'PU')->latest()->paginate(25);

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('events.partials.index', compact('events'))->render(),
            ], 200);
        }

        return view('events.index', compact('events'));
    }

    public function store(Request $request)
    {
        $userId = Auth::user()->id;

        if (! $userId) {
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
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $dateRangeType = $request->get('date_range_type');
        $eventType = $request->get('event_type');
        $eventcategoryId = $request->get('event_category_id');

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

        if ($request->get('date_range_type') == 'within') {
            if (empty(trim($endDate))) {
                $errors['end_date'][] = 'End date is required';
            }
        } else {
            $endDate = null;
        }

        if (! empty($errors)) {
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
        $event->event_type = $eventType;
        $event->date_range_type = $dateRangeType;
        $event->event_category_id = $eventcategoryId;
        $event->save();

        // Event Availabilities
        foreach ($request->event_availability as $key => $event_availability) {
            if (isset($event_availability['day']) && $event_availability['day'] == 'on') {
                $eventAvailability = new EventAvailability();
                $eventAvailability->event_id = $event->id;
                $eventAvailability->numeric_day = $key;
                $eventAvailability->start_at = date('H:i:s', strtotime($event_availability['start_at']));
                $eventAvailability->end_at = date('H:i:s', strtotime($event_availability['end_at']));
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

    public function deleteSchedule(Request $request, $id)
    {
        $result = EventSchedule::where('id', $id)->first();

        if ($result) {
            $result->delete();

            return response()->json([
                'code' => 200,
                'message' => 'Event Schedule deleted successfully !!',
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

            $result->date_range_type = 'within';
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

        if (! $userId) {
            return response()->json(
                [
                    'message' => 'Not allowed',
                ],
                401
            );
        }

        $event = Event::where('id', $request->get('id'))->myEvents(Auth::user()->id)->first();
        if (! $event) {
            return response()->json([
                'message' => 'Failed to reschedule',
                404,
            ]);
        }

        $durationInMin = $request->get('duration_in_min');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $mailBody = $request->get('mail_body');
        $dateRangeType = $request->get('date_range_type');

        $errors = [];
        if (empty(trim($startDate))) {
            $errors['start_date'][] = 'Start Date is required';
        }

        if (empty(trim($durationInMin))) {
            $errors['duration_in_min'][] = 'Duration is required';
        }

        if (empty(trim($mailBody))) {
            $errors['mail_body'][] = 'Mail body is required';
        }

        if ($dateRangeType == 'within') {
            if (empty(trim($endDate))) {
                $errors['end_date'][] = 'End date is required';
            }
        } else {
            $endDate = null;
        }

        if (! empty($errors)) {
            return response()->json($errors, 400);
        }

        // Event
        $event->start_date = $startDate;
        $event->end_date = $endDate;
        $event->duration_in_min = $durationInMin;
        $event->date_range_type = $dateRangeType;
        $event->save();

        if ($event->eventSchedules->count() > 0) {
            foreach ($event->eventSchedules as $eventSchedule) {
                $eventSchedule->delete(); // Delete already booked schedule for this event.
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
        $subject = 'Event Rescheduled';
        $emailClass = (new EventEmail($subject, $message, $event->user->email, $event->link))->build();

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

    public function getSchedules(Request $request)
    {
        $userId = Auth::user()->id;
        $user = Auth::user();

        if (! $userId) {
            return response()->json(
                [
                    'message' => 'Not allowed',
                ],
                401
            );
        }

        $start = explode('T', $request->get('start'))[0];
        $end = explode('T', $request->get('end'))[0];

        $eventSchedules = EventSchedule::whereBetween('schedule_date', [$start, $end])
            ->whereHas('event', function ($event) use ($userId) {
                $event->where('user_id', $userId)->where('event_type', 'PU');
            })
            ->get()
            ->map(function ($eventSchedule) {
                return [
                    'event_id' => $eventSchedule->event_id,
                    'event_schedule_id' => $eventSchedule->id,
                    'subject' => $eventSchedule->event->name,
                    'title' => $eventSchedule->event->name,
                    'description' => $eventSchedule->event->description,
                    'start' => $eventSchedule->schedule_date . ' ' . $eventSchedule->start_at,
                    'end' => $eventSchedule->schedule_date . ' ' . $eventSchedule->end_at,
                    'event_type' => $eventSchedule->event->event_type,
                ];
            });

        // Private Events
        $userPrivateEvents = Event::join('event_availabilities', 'event_availabilities.event_id', '=', 'events.id')
            ->select('events.*', 'event_availabilities.numeric_day', 'event_availabilities.start_at', 'event_availabilities.end_at')
            ->where('user_id', $userId)
            ->where('event_type', 'PR')
            ->where(function ($query) use ($start, $end) {
                $query->orWhereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere([
                        ['start_date', '<=', $start],
                        ['end_date', '=', null],
                    ]);
            })
            ->get();

        $userPrivateEventCollection = new Collection();

        foreach ($userPrivateEvents as $userPrivateEvent) {
            $eventEndDate = $userPrivateEvent->end_date ?: $end;
            $startDate = new Carbon($userPrivateEvent->start_date);
            $endDate = new Carbon($eventEndDate);

            while ($startDate->lte($endDate)) {
                if ($startDate->format('N') == $userPrivateEvent->numeric_day) {
                    $userPrivateEventCollection->push((object) [
                        'event_id' => $userPrivateEvent->id,
                        'subject' => $userPrivateEvent->name,
                        'title' => $userPrivateEvent->name,
                        'description' => $userPrivateEvent->description,
                        'start' => $startDate->toDateString() . ' ' . $userPrivateEvent->start_at,
                        'end' => $startDate->toDateString() . ' ' . $userPrivateEvent->end_at,
                        'event_type' => $userPrivateEvent->event_type,
                    ]);
                }
                $startDate->addDay();
            }
        }

        // Asset manager
        $myAssets = new Collection();
        $admin = $user->isAdmin();
        if ($admin) {
            $assetsManagers = AssetsManager::where([
                'active' => 1,
            ])
            ->whereIn('payment_cycle', ['Monthly', 'Yearly', 'One time'])
            ->whereNotNull('due_date')
            ->where(function ($query) use ($start, $end) {
                $query->WhereBetween('due_date', [$start, $end])
                    ->orWhere([
                        ['due_date', '<=', $start],
                    ]);
            })
            ->get();

            $cStartDate = Carbon::parse($start);
            $cEndDate = Carbon::parse($end);
            foreach ($assetsManagers as $assetsManager) {
                $cDueDate = Carbon::parse($assetsManager->due_date);
                // Monthly Payment Cycle - Logic
                if ($assetsManager->payment_cycle == 'Monthly') {
                    if (($cDueDate->month <= $cStartDate->month && $cDueDate->year == $cStartDate->year) ||
                    ($cDueDate->year < $cStartDate->year)) {
                        $myAssets->push((object) [
                            'assets_manager_id' => $assetsManager->id,
                            'subject' => 'Payment Due' . ' (Asset: ' . ($assetsManager->name ?? '-') . ", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'title' => 'Payment Due' . ' (Asset: ' . ($assetsManager->name ?? '-') . ", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'description' => "Provider name: $assetsManager->provider_name, Location: $assetsManager->location",
                            'start' => $cDueDate->setMonth($cStartDate->month)->setYear($cStartDate->year)->toDateString(),
                            'end' => $cDueDate->setMonth($cStartDate->month)->setYear($cStartDate->year)->toDateString(),
                            'event_type' => 'AS',
                        ]);
                    }
                }
                // Yearly Payment Cycle - Logic
                if ($assetsManager->payment_cycle == 'Yearly') {
                    if (($cDueDate->month == $cStartDate->month && $cDueDate->year <= $cStartDate->year)) {
                        $myAssets->push((object) [
                            'assets_manager_id' => $assetsManager->id,
                            'subject' => 'Payment Due' . ' (Asset: ' . ($assetsManager->name ?? '-') . ", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'title' => 'Payment Due' . ' (Asset: ' . ($assetsManager->name ?? '-') . ", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'description' => "Provider name: $assetsManager->provider_name, Location: $assetsManager->location",
                            'start' => $cDueDate->setMonth($cStartDate->month)->setYear($cStartDate->year)->toDateString(),
                            'end' => $cDueDate->setMonth($cStartDate->month)->setYear($cStartDate->year)->toDateString(),
                            'event_type' => 'AS',
                        ]);
                    }
                }
                // One time Payment Cycle - Logic
                if ($assetsManager->payment_cycle == 'One time') {
                    if (($cDueDate->month == $cStartDate->month && $cDueDate->year == $cStartDate->year)) {
                        $myAssets->push((object) [
                            'assets_manager_id' => $assetsManager->id,
                            'subject' => 'Payment Due' . ' (Asset: ' . ($assetsManager->name ?? '-') . ", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'title' => 'Payment Due' . ' (Asset: ' . ($assetsManager->name ?? '-') . ", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'description' => "Provider name: $assetsManager->provider_name, Location: $assetsManager->location",
                            'start' => $cDueDate->setMonth($cStartDate->month)->setYear($cStartDate->year)->toDateString(),
                            'end' => $cDueDate->setMonth($cStartDate->month)->setYear($cStartDate->year)->toDateString(),
                            'event_type' => 'AS',
                        ]);
                    }
                }
            }
        }

        $merged = $eventSchedules->concat($userPrivateEventCollection)->concat($myAssets);

        return response()->json($merged);
    }

    public function getEventAlerts(Request $request)
    {
        $userId = Auth::user()->id;
        $user = Auth::user();
        $dt = Carbon::now()->startOfDay();

        if ($request->get('date')) {
            $dt = Carbon::createFromFormat('Y-m-d', $request->get('date'));
        }

        // Public events
        $publicEventSchedules = EventSchedule::whereDate('schedule_date', $dt->toDateString())
            ->whereHas('event', function ($event) use ($userId) {
                $event->where('user_id', $userId)->where('event_type', 'PU');
            })
            ->whereDoesntHave('eventAlertLogs', function ($q) {
                $q->where('is_read', 1);
            })
            ->get()
            ->map(function ($eventSchedule) {
                return (object) [
                    'event_id' => $eventSchedule->event_id,
                    'event_schedule_id' => $eventSchedule->id,
                    'assets_manager_id' => '',
                    'subject' => $eventSchedule->event->name,
                    'title' => $eventSchedule->event->name,
                    'description' => $eventSchedule->event->description,
                    'start' => $eventSchedule->schedule_date . ' ' . $eventSchedule->start_at,
                    'end' => $eventSchedule->schedule_date . ' ' . $eventSchedule->end_at,
                    'event_type' => $eventSchedule->event->event_type,
                    'event_type_name' => $eventSchedule->event->event_type_name,
                ];
            });

        // Asset manager
        $myAssets = new Collection();
        $admin = $user->isAdmin();
        if ($admin) {
            $assetsManagers = AssetsManager::where([
                'active' => 1,
            ])
            ->whereIn('payment_cycle', ['Monthly', 'Yearly', 'One time'])
            ->whereNotNull('due_date')
            ->whereDoesntHave('eventAlertLogs', function ($q) use ($dt) {
                $q->where('is_read', 1);
                $q->where('event_alert_date', $dt->toDateString());
            })
            ->where(function ($query) use ($dt) {
                $query->WhereDate('due_date', $dt->toDateString())
                    ->orWhere([
                        ['due_date', '<=', $dt->toDateString()],
                    ]);
            })
            ->get();

            foreach ($assetsManagers as $assetsManager) {
                $cDueDate = Carbon::parse($assetsManager->due_date);
                // Monthly Payment Cycle - Logic
                if ($assetsManager->payment_cycle == 'Monthly') {
                    $monthlyRecurringDueDate = $cDueDate->setMonth($dt->month)->setYear($dt->year);

                    if ($dt->eq($monthlyRecurringDueDate)) {
                        $myAssets->push((object) [
                            'event_id' => '',
                            'event_schedule_id' => '',
                            'assets_manager_id' => $assetsManager->id,
                            'subject' => 'Payment Due' . ' (Asset: ' . ($assetsManager->name ?? '-') . ", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'title' => 'Payment Due' . ' (Asset: ' . ($assetsManager->name ?? '-') . ", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'description' => "Provider name: $assetsManager->provider_name, Location: $assetsManager->location",
                            'start' => $cDueDate->setMonth($dt->month)->setYear($dt->year)->toDateString(),
                            'end' => $cDueDate->setMonth($dt->month)->setYear($dt->year)->toDateString(),
                            'event_type' => Event::ASSET,
                            'event_type_name' => Event::$eventTypes[Event::ASSET],
                        ]);
                    }
                }
                // Yearly Payment Cycle - Logic
                if ($assetsManager->payment_cycle == 'Yearly') {
                    $yearlyRecurringDueDate = $cDueDate->setYear($dt->year);

                    if ($dt->eq($yearlyRecurringDueDate)) {
                        $myAssets->push((object) [
                            'event_id' => '',
                            'event_schedule_id' => '',
                            'assets_manager_id' => $assetsManager->id,
                            'subject' => 'Payment Due' . ' (Asset: ' . ($assetsManager->name ?? '-') . ", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'title' => 'Payment Due' . ' (Asset: ' . ($assetsManager->name ?? '-') . ", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'description' => "Provider name: $assetsManager->provider_name, Location: $assetsManager->location",
                            'start' => $cDueDate->setYear($dt->year)->toDateString(),
                            'end' => $cDueDate->setYear($dt->year)->toDateString(),
                            'event_type' => Event::ASSET,
                            'event_type_name' => Event::$eventTypes[Event::ASSET],
                        ]);
                    }
                }
                // One time Payment Cycle - Logic
                if ($assetsManager->payment_cycle == 'One time') {
                    if ($dt->eq($cDueDate)) {
                        $myAssets->push((object) [
                            'event_id' => '',
                            'event_schedule_id' => '',
                            'assets_manager_id' => $assetsManager->id,
                            'subject' => 'Payment Due' . ' (Asset: ' . ($assetsManager->name ?? '-') . ", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'title' => 'Payment Due' . ' (Asset: ' . ($assetsManager->name ?? '-') . ", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'description' => "Provider name: $assetsManager->provider_name, Location: $assetsManager->location",
                            'start' => $cDueDate->toDateString(),
                            'end' => $cDueDate->toDateString(),
                            'event_type' => Event::ASSET,
                            'event_type_name' => Event::$eventTypes[Event::ASSET],
                        ]);
                    }
                }
            }
        }

        // Private Events
        $userPrivateEvents = Event::join('event_availabilities', 'event_availabilities.event_id', '=', 'events.id')
            ->select('events.*', 'event_availabilities.numeric_day', 'event_availabilities.start_at', 'event_availabilities.end_at')
            ->where('user_id', $userId)
            ->where('event_type', 'PR')
            ->where(function ($query) use ($dt) {
                $query->where([
                    ['start_date', '<=', $dt->toDateString()],
                    ['end_date', '>=', $dt->toDateString()],
                ])
                    ->orWhere([
                        ['start_date', '<=', $dt->toDateString()],
                        ['end_date', '=', null],
                    ]);
            })
            ->get();

        $userPrivateEventCollection = new Collection();
        foreach ($userPrivateEvents as $userPrivateEvent) {
            if ($dt->format('N') == $userPrivateEvent->numeric_day) {
                $eventAlertDate = $dt->toDateString() . ' ' . $userPrivateEvent->start_at;
                $eventAlertLogExists = $userPrivateEvent->whereHas('eventAlertLogs', function ($eventAlertLog) use ($userId, $eventAlertDate) {
                    $eventAlertLog->where('user_id', $userId)
                        ->where('is_read', 1)
                        ->where('event_alert_date', $eventAlertDate)
                        ->where('event_type', 'PR');
                })->exists();

                if (! $eventAlertLogExists) {
                    $userPrivateEventCollection->push((object) [
                        'event_id' => $userPrivateEvent->id,
                        'event_schedule_id' => '',
                        'assets_manager_id' => '',
                        'subject' => $userPrivateEvent->name,
                        'title' => $userPrivateEvent->name,
                        'description' => $userPrivateEvent->description,
                        'start' => $dt->toDateString() . ' ' . $userPrivateEvent->start_at,
                        'end' => $dt->toDateString() . ' ' . $userPrivateEvent->end_at,
                        'event_type' => $userPrivateEvent->event_type,
                        'event_type_name' => $userPrivateEvent->event_type_name,
                    ]);
                }
            }
        }

        $merged = $publicEventSchedules->concat($myAssets)->concat($userPrivateEventCollection);

        $html = view('partials.modals.event-alerts-modal-html')->with('eventAlerts', $merged)->render();

        return response()->json(['code' => 200, 'html' => $html, 'message' => 'Content render', 'count' => $merged->count()]);
    }

    public function saveAlertLog(Request $request)
    {
        $eventType = $request->event_type;
        $eventId = $request->event_id;
        $eventScheduleId = $request->event_schedule_id;
        $assetsManagerId = $request->assets_manager_id;
        $eventAlertDate = $request->event_alert_date;
        $isRead = ($request->is_read == 'true' ? 1 : 0);

        if ($eventType == Event::PUBLIC) {
            $eventSchedule = EventSchedule::findOrFail($eventScheduleId);

            $eventAlertLog['user_id'] = Auth::user()->id;
            $eventAlertLog['event_alert_date'] = $eventAlertDate;
            $eventAlertLog['event_type'] = $eventType;

            $eventSchedule->eventAlertLogs()->updateOrCreate($eventAlertLog, ['is_read' => $isRead]);
        }

        if ($eventType == Event::PRIVATE) {
            $event = Event::findOrFail($eventId);

            $eventAlertLog['user_id'] = Auth::user()->id;
            $eventAlertLog['event_alert_date'] = $eventAlertDate;
            $eventAlertLog['event_type'] = $eventType;

            $event->eventAlertLogs()->updateOrCreate($eventAlertLog, ['is_read' => $isRead]);
        }

        if ($eventType == Event::ASSET) {
            $assetsManager = AssetsManager::findOrFail($assetsManagerId);

            $eventAlertLog['user_id'] = Auth::user()->id;
            $eventAlertLog['event_alert_date'] = $eventAlertDate;
            $eventAlertLog['event_type'] = $eventType;

            $assetsManager->eventAlertLogs()->updateOrCreate($eventAlertLog, ['is_read' => $isRead]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Updated successfully !!',
        ]);
    }

    public function eventCategoryStore(Request $request)
    {
        $eventCategory = new EventCategory();
        $eventCategory->category = $request->category;
        $eventCategory->save();

        return response()->json(['code' => 200, 'data' => $eventCategory, 'message' => 'Category create Succcesfully']);
    }
}
