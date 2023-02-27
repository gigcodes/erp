<?php

namespace App\Http\Controllers;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;

class SentryLogController extends Controller
{
    public function index()
    {
        $url = 'https://sentry.io/api/0/projects/'.env('SENTRY_ORGANIZATION').'/'.env('SENTRY_PROJECT').'/issues/';        
        $httpClient = new Client();

        $response = $httpClient->get(
            $url,
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer '.env('SENTRY_TOKEN'),
                ],
            ]
        );
        $responseJson = json_decode($response->getBody()->getContents());
        $sentryLogsData = [];
        
        foreach( $responseJson as $error_log){
            $res['id'] = $error_log->id;
            $res['title'] = $error_log->title;
            $res['issue_type'] = $error_log->issueType;
            $res['issue_category'] = $error_log->issueCategory;
            $res['is_unhandled'] = $error_log->isUnhandled;
            $res['first_seen'] = $error_log->firstSeen;
            $res['last_seen'] = $error_log->lastSeen;
            $sentryLogsData[] = $res;
        }
        
        return view('sentry-log.index', compact('sentryLogsData'));
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

        $url = 'https://sentry.io/api/0/projects/'.env('SENTRY_ORGANIZATION').'/'.env('SENTRY_PROJECT').'/issues/';
        $httpClient = new Client();

        $response = $httpClient->get(
            $url,
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer '.env('SENTRY_TOKEN'),
                ],
            ]
        );
        $responseJson = json_decode($response->getBody()->getContents());

        $sentryLogsData = [];
        $totalRecods = 0;
        for ($i=0;$i<100;$i++){
            foreach( $responseJson as $error_log){
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
}
