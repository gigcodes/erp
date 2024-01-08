<?php

namespace App\Http\Controllers;

use App\Models\AppointmentRequest;
use App\User;
use Exception;
use App\TestCase;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Jobs\UploadGoogleDriveScreencast;
use Illuminate\Support\Facades\Validator;

class AppointmentRequestController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Appointment Request';

        $records = AppointmentRequest::select('*')->orderBy('id', 'DESC')->get();
        $records_count = $records->count();

        return view(
            'appointment-request.index', [
                'title' => $title,
                'records_count' => $records_count,
            ]
        );
    }

    public function records(Request $request)
    {   
        $records = AppointmentRequest::with('user', 'userrequest')->select('*')->orderBy('id', 'DESC');

        /*if ($keyword = request('keyword')) {
            $records = $records->where(
                function ($q) use ($keyword) {
                    $q->where('file', 'LIKE', "%$keyword%");
                }
            );
        }*/

        $records = $records->take(25)->get();
        $records_count = $records->count();

        $records = $records->map(
            function ($script_document) {
                $script_document->created_at_date = \Carbon\Carbon::parse($script_document->created_at)->format('d-m-Y');
                return $script_document;
            }
        );

        return response()->json(
            [
                'code' => 200,
                'data' => $records,
                'total' => $records_count,
            ]
        );
    }

    public function recordAppointmentRequestAjax(Request $request)
    {
        $title = 'Appointment Request';
        $page = $_REQUEST['page'];
        $page = $page * 25;

        $records = AppointmentRequest::with('user', 'userrequest')->select('*')->orderBy('id', 'DESC')->offset($page)->limit(25);

        /*if ($keyword = request('keyword')) {
            $records = $records->where(
                function ($q) use ($keyword) {
                    $q->where('file', 'LIKE', "%$keyword%");
                }
            );
        }*/

        $records = $records->get();

        $records = $records->map(
            function ($script_document) {
                $script_document->created_at_date = \Carbon\Carbon::parse($script_document->created_at)->format('d-m-Y');                
                return $script_document;
            }
        );

        $records;

        // return response()->json(['code' => 200, 'data' => $records, 'total' => count($records)]);

        return view(
            'appointment-request.index-ajax', [
                'title' => $title,
                'data' => $records,
                'total' => count($records),
            ]
        );
    }
}
