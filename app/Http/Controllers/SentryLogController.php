<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Sentry\SentryAccount;
use App\Sentry\SentryErrorLog;
use GuzzleHttp\RequestOptions;
use App\Models\SentyStatus;

class SentryLogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->project_list) {
            $sentry_logs = SentryErrorLog::where('project_id', $request->project_list)->get();
        } else {
            $sentry_logs = SentryErrorLog::get();
        }

        $project_list = SentryAccount::get();
        $sentryLogsData = [];
        $projects = [];
        foreach ($sentry_logs as $error_log) {
            $res['id'] = $error_log->error_id;
            $res['title'] = $error_log->error_title;
            $res['issue_type'] = $error_log->issue_type;
            $res['issue_category'] = $error_log->issue_category;
            $res['is_unhandled'] = $error_log->is_unhandled;
            $res['project'] = $error_log->sentry_project->sentry_project;
            $res['total_events'] = $error_log->total_events;
            $res['total_user'] = $error_log->total_user;
            $res['device_name'] = $error_log->device_name;
            $res['os'] = $error_log->os;
            $res['os_name'] = $error_log->os_name;
            $res['release_version'] = $error_log->release_version;
            $res['first_seen'] = $error_log->first_seen;
            $res['last_seen'] = $error_log->last_seen;
            $sentryLogsData[] = $res;
        }
        foreach ($project_list as $project) {
            $data['id'] = $project->id;
            $data['name'] = $project->sentry_project;
            $projects[] = $data;
        }

        $status = SentyStatus::all();

        return view('sentry-log.index', compact('sentryLogsData', 'projects', 'status'));
    }

    public function getSentryLogData(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'title',
            2 => 'issue_type',
            3 => 'issue_category',
            4 => 'is_unhandled',
            5 => 'first_seen',
            6 => 'last_seen',
        ];

        /*  $limit = $request->input('length');
          $start = $request->input('start');

          $suppliercount = SupplierBrandCount::query();
          $suppliercountTotal = SupplierBrandCount::count();
          $supplier_list = Supplier::where('supplier_status_id', 1)->orderby('supplier', 'asc')->get();
          $brand_list = Brand::orderby('name', 'asc')->get();
          $category_parent = Category::where('parent_id', 0)->orderby('title', 'asc')->get();
          $category_child = Category::where('parent_id', '!=', 0)->orderby('title', 'asc')->get();

          $suppliercount = $suppliercount->offset($start)->limit($limit)->orderBy('supplier_id', 'asc')->get();*/

        $url = 'https://sentry.io/api/0/projects/' . env('SENTRY_ORGANIZATION') . '/' . env('SENTRY_PROJECT') . '/issues/';
        $httpClient = new Client();

        $response = $httpClient->get(
            $url,
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . env('SENTRY_TOKEN'),
                ],
            ]
        );
        $responseJson = json_decode($response->getBody()->getContents());

        $sentryLogsData = [];
        $totalRecods = 0;
        for ($i = 0; $i < 100; $i++) {
            foreach ($responseJson as $error_log) {
                $res['id'] = $error_log->id;
                $res['title'] = $error_log->title;
                $res['issue_type'] = $error_log->issueType;
                $res['issue_category'] = $error_log->issueCategory;
                $res['is_unhandled'] = $error_log->isUnhandled;
                $res['first_seen'] = $error_log->firstSeen;
                $res['last_seen'] = $error_log->lastSeen;
                $sentryLogsData[] = $res;
                $totalRecods++;
            }
        }

        foreach ($sentryLogsData as $error_log) {
            $sub_array = [];
            $sub_array[] = $error_log['id'];
            $sub_array[] = $error_log['title'];
            $sub_array[] = $error_log['issue_type'];
            $sub_array[] = $error_log['issue_category'];
            $sub_array[] = $error_log['is_unhandled'];
            $sub_array[] = $error_log['first_seen'];
            $sub_array[] = $error_log['last_seen'];
            $data[] = $sub_array;
        }

        // dd(count($data));
        if (! empty($data)) {
            $output = [
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecods,
                'recordsFiltered' => $totalRecods,
                'data' => $data,
            ];
        } else {
            $output = [
                'draw' => 0,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ];
        }

        return json_encode($output);
    }

    public function saveUserAccount(Request $request)
    {
        try {
            $sentry_acount = new SentryAccount();
            $sentry_acount->sentry_project = $request->project;
            $sentry_acount->sentry_organization = $request->organization;
            $sentry_acount->sentry_token = $request->token;

            if ($sentry_acount->save()) {
                $url = 'https://sentry.io/api/0/projects/' . $sentry_acount->sentry_organization . '/' . $sentry_acount->sentry_project . '/issues/';
                $httpClient = new Client();

                $response = $httpClient->get(
                    $url,
                    [
                        RequestOptions::HEADERS => [
                            'Authorization' => 'Bearer ' . $sentry_acount->sentry_token,
                        ],
                    ]
                );
                $responseJson = json_decode($response->getBody()->getContents());

                foreach ($responseJson as $error_log) {
                    SentryErrorLog::create([
                        'error_id' => $error_log->id,
                        'error_title' => $error_log->title,
                        'issue_type' => $error_log->issueType,
                        'issue_category' => $error_log->issueCategory,
                        'is_unhandled' => ($error_log->isUnhandled == 'false') ? 0 : 1,
                        'first_seen' => date('d-m-y H:i:s', strtotime($error_log->firstSeen)),
                        'last_seen' => date('d-m-y H:i:s', strtotime($error_log->lastSeen)),
                        'project_id' => $sentry_acount->id,
                    ]);
                }

                return response()->json(['code' => 200, 'data' => [], 'message' => 'Sentry Account Added successfully']);
            } else {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
            }
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
        }
    }

    public function displayUserAccountList(Request $request)
    {
        $sentryAccounts = SentryAccount::all();
        $html = '';
        $i = 1;
        foreach ($sentryAccounts as $account) {
            $html .= '<tr>';
            $html .= '<td>' . $i++ . '</td>';
            $html .= '<td>' . $account->sentry_organization . '</td>';
            $html .= '<td>' . $account->sentry_project . '</td>';
            $html .= "<td style='vertical-align:middle;'>" . $account->sentry_token . '</td>';
            $html .= '</tr>';
        }

        return $html;
    }

    public function refreshLogs()
    {
        \Artisan::call('sentry:load_error_logs');

        return redirect()->back();
    }

    public function sentryStatusCreate(Request $request)
    {
        try {
            $status = new SentyStatus();
            $status->status_name = $request->status_name;
            $status->save();

            return response()->json(['code' => 200, 'message' => 'status Create successfully']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function statuscolor(Request $request)
    {
        $status_color = $request->all();
        $data = $request->except('_token');
        foreach ($status_color['color_name'] as $key => $value) {
            $bugstatus = SentyStatus::find($key);
            $bugstatus->senty_color = $value;
            $bugstatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }
}
