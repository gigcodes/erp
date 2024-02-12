<?php

namespace App\Http\Controllers\AppConnect;

use App\AppAdsReport;
use App\AppSalesReport;
use App\AppUsageReport;
use App\AppPaymentReport;
use App\AppRatingsReport;
use Illuminate\Http\Request;
use App\AppSubscriptionReport;
use App\Models\DataTableColumn;
use App\Http\Controllers\Controller;

class AppConnectController extends Controller
{
    public function getUsageReport()
    {
        $id = 0;
        $reports = AppUsageReport::groupBy('start_date')->get();

        return view('appconnect.app-users', ['reports' => $reports, 'id' => $id]);
    }

    public function getRatingsReport()
    {
        $id = 0;
        $reports = AppRatingsReport::groupBy('start_date')->get();

        return view('appconnect.app-rate', ['reports' => $reports, 'id' => $id]);
    }

    public function getSalesReport()
    {
        $id = 0;
        $reports = AppSalesReport::groupBy('start_date')->get();

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'app-sales-listing')->first();

        $dynamicColumnsToShowb = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowb = json_decode($hideColumns, true);
        }

        return view('appconnect.app-sales', ['reports' => $reports, 'id' => $id, 'dynamicColumnsToShowb' => $dynamicColumnsToShowb]);
    }

    public function columnVisibilityUpdateAppSales(Request $request)
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'app-sales-listing')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'app-sales-listing';
            $column->column_name = json_encode($request->column_data);
            $column->save();
        } else {
            $column = new DataTableColumn();
            $column->section_name = 'app-sales-listing';
            $column->column_name = json_encode($request->column_data);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function getPaymentReport()
    {
        $id = 0;
        $reports = AppPaymentReport::groupBy('start_date')->get();

        return view('appconnect.app-pay', ['reports' => $reports, 'id' => $id]);
    }

    public function getAdsReport()
    {
        $id = 0;
        $reports = AppAdsReport::groupBy('start_date')->get();

        return view('appconnect.app-ads', ['reports' => $reports, 'id' => $id]);
    }

    public function getSubscriptionReport(Request $request)
    {
        $id = 0;
        $reports = AppSubscriptionReport::groupBy('start_date')->get();

        return view('appconnect.app-sub', ['reports' => $reports, 'id' => $id]);
    }

    public function getUsageReportfilter(Request $request)
    {
        $reports = AppUsageReport::groupBy('start_date');
        if ($request->input('app_name')) {
            $app_name = $request->input('app_name');
            $reports = $reports->Where('product_id', 'like', '%' . $app_name . '%');
        }
        if ($request->input('fdate')) {
            $fdate = $request->input('fdate');
            if ($request->input('edate')) {
                $edate = $request->input('edate');
            } else {
                $edate = $fdate;
            }
            $reports = $reports->whereDate('start_date', '>=', $fdate)
                ->whereDate('start_date', '<=', $edate);
        }

        $id = 0;
        $reports = $reports->get();

        return view('appconnect.app-users', ['reports' => $reports, 'id' => $id]);
    }

    public function getRatingsReportfilter(Request $request)
    {
        $reports = AppRatingsReport::groupBy('start_date');
        if ($request->input('app_name')) {
            $app_name = $request->input('app_name');
            $reports = $reports->Where('product_id', 'like', '%' . $app_name . '%');
        }

        $id = 0;

        if ($request->input('fdate')) {
            $fdate = $request->input('fdate');
            if ($request->input('edate')) {
                $edate = $request->input('edate');
            } else {
                $edate = $fdate;
            }
            $reports = $reports->whereDate('start_date', '>=', $fdate)
                ->whereDate('start_date', '<=', $edate);
        }

        $id = 0;
        $reports = $reports->get();

        return view('appconnect.app-rate', ['reports' => $reports, 'id' => $id]);
    }

    public function getSalesReportfilter(Request $request)
    {
        $reports = AppSalesReport::groupBy('start_date');
        if ($request->input('app_name')) {
            $app_name = $request->input('app_name');
            $reports = $reports->Where('product_id', 'like', '%' . $app_name . '%');
        }

        $id = 0;

        if ($request->input('fdate')) {
            $fdate = $request->input('fdate');
            if ($request->input('edate')) {
                $edate = $request->input('edate');
            } else {
                $edate = $fdate;
            }
            $reports = $reports->whereDate('start_date', '>=', $fdate)
                ->whereDate('start_date', '<=', $edate);
        }

        $id = 0;
        $reports = $reports->get();

        return view('appconnect.app-sales', ['reports' => $reports, 'id' => $id]);
    }

    public function getPaymentReportfilter(Request $request)
    {
        $reports = AppPaymentReport::groupBy('start_date');
        if ($request->input('app_name')) {
            $app_name = $request->input('app_name');
            $reports = $reports->Where('product_id', 'like', '%' . $app_name . '%');
        }
        $id = 0;

        if ($request->input('fdate')) {
            $fdate = $request->input('fdate');
            if ($request->input('edate')) {
                $edate = $request->input('edate');
            } else {
                $edate = $fdate;
            }
            $reports = $reports->whereDate('start_date', '>=', $fdate)
                ->whereDate('start_date', '<=', $edate);
        }

        $id = 0;
        $reports = $reports->get();

        return view('appconnect.app-pay', ['reports' => $reports, 'id' => $id]);
    }

    public function getAdsReportfilter(Request $request)
    {
        $reports = AppAdsReport::groupBy('start_date');
        if ($request->input('app_name')) {
            $app_name = $request->input('app_name');
            $reports = $reports->Where('product_id', 'like', '%' . $app_name . '%');
        }

        $id = 0;

        if ($request->input('fdate')) {
            $fdate = $request->input('fdate');
            if ($request->input('edate')) {
                $edate = $request->input('edate');
            } else {
                $edate = $fdate;
            }
            $reports = $reports->whereDate('start_date', '>=', $fdate)
                ->whereDate('start_date', '<=', $edate);
        }

        $id = 0;
        $reports = $reports->get();

        return view('appconnect.app-ads', ['reports' => $reports, 'id' => $id]);
    }

    public function getSubscriptionReportfilter(Request $request)
    {
        $reports = AppSubscriptionReport::groupBy('start_date');
        if ($request->input('app_name')) {
            $app_name = $request->input('app_name');
            $reports = $reports->Where('product_id', 'like', '%' . $app_name . '%');
        }
        $id = 0;

        if ($request->input('fdate')) {
            $fdate = $request->input('fdate');
            if ($request->input('edate')) {
                $edate = $request->input('edate');
            } else {
                $edate = $fdate;
            }
            $reports = $reports->whereDate('start_date', '>=', $fdate)
                ->whereDate('start_date', '<=', $edate);
        }

        $id = 0;
        $reports = $reports->get();

        return view('appconnect.app-sub', ['reports' => $reports, 'id' => $id]);
    }
}
