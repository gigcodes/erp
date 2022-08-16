<?php

namespace App\Http\Controllers;

use App\UserAvaibility;

class UserAvaibilityController extends Controller {

    public function __construct() {
    }

    public function index() {
        return respJson(200, '', ['data' => $this->loadIndex(request('id'))]);
    }
    public function loadIndex($userId) {
        $q = UserAvaibility::query();
        if ($s = $userId) {
            $q->where('user_id', $s);
        }
        if (!isAdmin()) {
            $q->where('user_id', loginId());
        }
        $list = $q->orderBy('id', 'DESC')->get();

        $html = [];
        $html[] = '<table class="table table-bordered">';
        $html[] = '<thead>
            <tr>
                <th width="5%">ID</th>
                <th width="20%" style="word-break: break-all;">From/To Date</th>
                <th width="15%" style="word-break: break-all;">Start/End Time</th>
                <th width="35%" style="word-break: break-all;">Available Days</th>
                <th width="10%" style="word-break: break-all;">Lunch Time</th>
                <th width="15%">Created at</th>
            </tr>
        </thead>';
        if ($list->count()) {
            foreach ($list as $single) {
                $html[] = '<tr>
                    <td>' . $single->id . '</td>
                    <td>' . $single->from . ' - ' . $single->to . '</td>
                    <td>' . $single->start_time . ' - ' . $single->end_time . '</td>
                    <td>' . (str_replace(',', ', ', $single->date) ?: '-') . '</td>
                    <td>' . ($single->lunch_time ?: '-') . '</td>
                    <td>' . $single->created_at . '</td>
                </tr>';
            }
        } else {
            $html[] = '<tr>
                <td colspan="5">No records found.</td>
            </tr>';
        }
        $html[] = '</table>';
        return implode('', $html);
    }

    public function save() {
        try {
            \Log::info('Request:' . json_encode(request()->all()));

            $user_id = request('user_id');
            if (!isAdmin()) {
                $user_id = loginId();
            }

            $errors = reqValidate(request()->all(), [
                'day' => 'required',
                'from' => 'required',
                'to' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
            ], [
                'day.required' => 'Days is required, please select atleast one.',
                'from.required' => 'From date is required.',
                'to.required' => 'To date is required.',
            ]);
            if ($errors) {
                return respJson(400, $errors[0]);
            }

            UserAvaibility::where('user_id', $user_id)->update(['is_latest' => 0]);

            UserAvaibility::create([
                'user_id' => $user_id,
                'from' => request('from'),
                'to' => request('to'),
                'status' => 1,
                'note' => null,
                'date' => implode(',', request('day')),
                'start_time' => request('start_time'),
                'end_time' => request('end_time'),
                'lunch_time' => request('lunch_time') ?: NULL,
                'is_latest' => 1,
            ]);

            return respJson(200, 'Added successfully.', [
                'list' => $this->loadIndex($user_id)
            ]);
        } catch (\Throwable $th) {
            return respException($th);
        }
    }
}
