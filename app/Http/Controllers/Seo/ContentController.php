<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Models\Seo\SeoHistory;
use Illuminate\Http\Request;
use App\StoreWebsite;
use App\Models\Seo\SeoProcessStatus;
use App\User;
use App\Models\Seo\SeoProcess;
use App\Models\Seo\SeoKeyword;
use App\Models\Seo\SeoKeywordRemark;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ContentController extends Controller
{
    private $route = "seo/content";
    private $view = "seo/content";

    public function index(Request $request)
    {
        if($request->ajax()) {      
            return $this->getAjaxSeoProcess();
        }
        return view("{$this->view}/index");
    }

    public function create(Request $request)
    {
        if($request->ajax()) {
            return $this->renderTeamStatus();
        }
        $data['storeWebsites'] = StoreWebsite::select('id', 'website')->get();
        $data['seoProcessStatus'] = SeoProcessStatus::all();
        $data['users'] = User::select('id', 'name')->get();
        return  view("$this->route/create", $data);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        $seoProcess = SeoProcess::create([
            'website_id' => $request->website_id,
            'user_id' => $request->user_id,
            'price' => $request->price,
            'is_price_approved' => $request->is_price_approved ?? 0,
            'google_doc_link' => $request->google_doc_link,
            'seo_process_status_id' => $request->seo_process_status_id,
            'live_status_link' => $request->live_status_link,
            'published_at' => $request->published_at,
            'status' => $request->status
        ]);
        $this->addPriceHistory($seoProcess, true);
        $this->addUserHistory($seoProcess, true);
        $this->addKeywordData($seoProcess);
        DB::commit();

        return Redirect::route('seo.content.index');
    }

    public function show(Request $request,int $id)
    {
        if($request->ajax()) {
            if($request->type == 'GET_HISTORY') {
                return $this->renderPriceHistory();
            }
            return $this->renderTeamStatus();
        }
        $data['seoProcess'] = SeoProcess::find($id);
        $data['storeWebsites'] = StoreWebsite::select('id', 'website')->get();
        $data['seoProcessStatus'] = SeoProcessStatus::all();
        $data['users'] = User::select('id', 'name')->get();
        return view("{$this->view}/view", $data);
    }

    public function edit(Request $request, int $id)
    {
        if($request->ajax()) {
            if($request->type == 'GET_HISTORY') {
                return $this->renderPriceHistory();
            }
            return $this->renderTeamStatus();
        }
        $data['seoProcess'] = SeoProcess::find($id);
        $data['storeWebsites'] = StoreWebsite::select('id', 'website')->get();
        $data['seoProcessStatus'] = SeoProcessStatus::all();
        $data['users'] = User::select('id', 'name')->get();
        return view("{$this->view}/edit", $data);
    }

    public function update(Request $request, int $id) 
    {
        DB::beginTransaction();
        $seoProcess = SeoProcess::find($id);
        $this->addPriceHistory($seoProcess, false);
        $this->addUserHistory($seoProcess, false);
        $seoProcess->update([
            'website_id' => $request->website_id,
            'user_id' => $request->user_id,
            'price' => $request->price,
            'is_price_approved' => $request->is_price_approved ?? 0,
            'google_doc_link' => $request->google_doc_link,
            'seo_process_status_id' => $request->seo_process_status_id,
            'live_status_link' => $request->live_status_link,
            'published_at' => $request->published_at,
            'status' => $request->status
        ]);
        $this->addKeywordData($seoProcess);
        DB::commit();

        return Redirect::route('seo.content.index');
    }

    private function addKeywordData(SeoProcess $seoProcess)
    {   
        $request = request();
        foreach($seoProcess->keywords as $keyword) {
            $keyword->seoRemarks()->delete();
            $keyword->publishRemarks()->delete();
            $keyword->delete();
        }

        foreach($request->keyword as $ky => $item) {
            $keyword = SeoKeyword::create([
                'seo_process_id' => $seoProcess->id,
                'keyword' => $item,
                'word_count' => $request->word_count[$ky] ?? null,
                'content' => $request->suggestion[$ky] ?? null,
                'status' => $request->kw_status[$ky] ?? null,
            ]);
            $seoStatusArr = [];
            if(!empty($request->seo_status[$ky])) {
                foreach(json_decode($request->seo_status[$ky]) as $item) {
                    $seoStatusArr[] = [
                        'seo_keywords_id' => $keyword->id,
                        'seo_process_status_id' => $item->seo_process_status_id,
                        'remarks' => $item->remarks,
                    ];
                }
                SeoKeywordRemark::insert($seoStatusArr);
            }

            $seoStatusArr = [];
            if(!empty($request->publish_status[$ky])) {
                foreach(json_decode($request->publish_status[$ky]) as $item) {
                    $seoStatusArr[] = [
                        'seo_keywords_id' => $keyword->id,
                        'seo_process_status_id' => $item->seo_process_status_id,
                        'remarks' => $item->remarks,
                    ];
                }
                SeoKeywordRemark::insert($seoStatusArr);
            }
        }
    }

    private function getAjaxSeoProcess()
    {
        $auth = auth()->user();
        $seoProcess = SeoProcess::with(['website:id,website', 'user:id,name']);
        if($auth->hasRole(['user'])) {
            $seoProcess = $seoProcess->where('user_id', $auth->id);
        }
        $datatable = datatables()->eloquent($seoProcess)
            ->addColumn('actions', function($val) use($auth) {
                $editUrl = route('seo.content.edit', $val->id);
                $showUrl = route('seo.content.show', $val->id);
                $actions = '';
                if($auth->hasRole(['Admin', 'user'])) {
                    $actions .= "<a href='{$editUrl}' class='btn btn-warning btn-sm'>Edit</a>";
                }
                $actions .= "<a href='{$showUrl}' class='btn btn-warning btn-sm ml-2'>Show</a>";
                return $actions;
            })
            ->addColumn('user_id', function($val) {
                return $val->user->name ?? '-';
            })
            ->addColumn('website_id', function($val) {
                return $val->website->website ?? '-';
            })
            ->editColumn('status', function($val) {
                $status = '';
                if($val->status == 'planned') {
                    $status = "<span class='badge btn'>Planned</span>";
                } else {
                    $status = "<span class='badge btn'>Admin approve</span>";
                }
                return $status;
            })
            ->rawColumns(['actions', 'status'])
            ->addIndexColumn()
            ->make();
        return $datatable;
    }

    private function renderTeamStatus()
    {
        $data['seoKeyword'] = null;
        if(!empty(request()->keywordId)) {
            $data['seoKeyword'] = SeoKeyword::find(request()->keywordId);
        }
        $data['statusType'] = request()->statusType;
        $data['seoProcessStatus'] = SeoProcessStatus::all();
        $html = view("{$this->view}/team-status-ajax", $data)->render();
        return response()->json([
            'success' => true,
            'data' => $html,
        ]);
    }

    private function addPriceHistory(SeoProcess $seoProcess, bool $isUpdate)
    {
        $request = request();
        if($request->price != $seoProcess->price || $isUpdate) {
            SeoHistory::create([
                'user_id' => auth()->id(),
                'type' => 'price',
                'seo_process_id' => $seoProcess->id,
                'message' => $request->price,
            ]);
        }
    }

    private function addUserHistory(SeoProcess $seoProcess, bool $isUpdate)
    {
        $request = request();
        if($request->user_id != $seoProcess->user_id || $isUpdate) {
            SeoHistory::create([
                'user_id' => auth()->id(),
                'type' => 'user',
                'seo_process_id' => $seoProcess->id,
                'message' => $request->user_id,
            ]);
        }
    }

    private function renderPriceHistory()
    {
        $request = request();
        $data['seoHistory'] = SeoHistory::query()->where('seo_process_id', $request->seoProcessId)
            ->where('type', $request->seoType)->get();
        $data['seoType'] = $request->seoType;
        $html = view("$this->view/history", $data)->render();
        return response()->json([
            'success' => true,
            'data' => $html,
        ]);
    }
}
