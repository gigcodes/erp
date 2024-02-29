<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\DailyActivity;
use App\UserEvent\UserEvent;
use Illuminate\Http\Request;

class DailyActivityController extends Controller
{
    public function store(Request $request)
    {
        $data = json_decode(urldecode($request->input('activity_table_data')), true);

        foreach ($data as $item) {
            if (! empty($item['id'])) {
                DailyActivity::updateOrCreate(['id' => $item['id']], $item);
            } else {
                $item['for_date'] = date('Y-m-d');
                $item['user_id']  = \Auth::id();
                DailyActivity::create($item);
            }
        }
    }

    public function quickStore(Request $request)
    {
        $data = $request->except('_token');

        // check first we need to add general categories first or not
        $generalCat = $request->get('general_category_id', null);

        if (! is_numeric($generalCat) && $generalCat != '') {
            $gc = \App\GeneralCategory::updateOrCreate(['name' => $generalCat], ['name' => $generalCat]);
            if (! empty($gc)) {
                $data['general_category_id'] = $gc->id;
            }
        }
        // general category store end

        // Save the data in user event
        $schedultDate = Carbon::parse($request->for_date);
        $timeSlotArr  = explode('-', $request->time_slot);
        $c_start_at   = Carbon::parse("$request->for_date " . $timeSlotArr[0]);
        $c_end_at     = Carbon::parse("$request->for_date " . $timeSlotArr[1]);

        $userEvent              = new UserEvent();
        $userEvent->user_id     = $request->user_id;
        $userEvent->description = trim($timeSlotArr[0]) . '-' . trim($timeSlotArr[1]) . ', ' . $schedultDate->format('l') . ', ' . $schedultDate->toDateString();
        $userEvent->subject     = $request->activity;
        $userEvent->date        = $schedultDate;
        $userEvent->start       = $c_start_at->toDateTime();
        $userEvent->end         = $c_end_at->toDateTime();
        $userEvent->save();

        $activity = DailyActivity::create($data);

        return response()->json([
            'activity' => $activity,
        ]);
    }

    public function complete(Request $request, $id)
    {
        $activity               = DailyActivity::find($id);
        $activity->is_completed = Carbon::now();
        $activity->save();

        return response('success', 200);
    }

    public function start(Request $request, $id)
    {
        $activity                    = DailyActivity::find($id);
        $activity->actual_start_date = Carbon::now();
        $activity->save();

        return response('success', 200);
    }

    public function get(Request $request)
    {
        $selected_user = $request->input('selected_user');
        $user_id       = $selected_user ?? \Auth::id();

        $activities = DailyActivity::where('user_id', $user_id)
            ->where('for_date', $request->daily_activity_date)->get()->toArray();

        return $activities;
    }
}
