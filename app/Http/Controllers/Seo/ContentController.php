<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Models\Seo\SeoHistory;
use Illuminate\Http\Request;
use App\StoreWebsite;
use App\Models\Seo\SeoProcessStatus;
use App\User;
use App\Models\Seo\SeoProcess;
use App\Models\Seo\SeoKeywordRemark;
use App\Models\Seo\SeoProcessKeyword;
use App\Models\Seo\SeoProcessRemark;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ContentController extends Controller
{
    private $route = "seo/content";
    private $view = "seo/content";
    private $moduleName = "SEO Content";

    public function index(Request $request)
    {
        if($request->ajax()) {   
            return $this->getAjaxSeoProcess();
        }
        $data['websites'] = StoreWebsite::select('id', 'website')->get();
        $data['users'] = User::select('id', 'name')->get();
        $data['moduleName'] = $this->moduleName;
        return view("{$this->view}/index", $data);
    }

    public function create()
    {
        $data['storeWebsites'] = StoreWebsite::select('id', 'website')->get();
        $data['seoProcessStatus'] = SeoProcessStatus::all();
        $data['users'] = User::select('id', 'name')->get();
        $data['moduleName'] = $this->moduleName;
        $html = view("$this->route/ajax/create", $data)->render();
        return response()->json([
            'success' => true,
            'data' => $html,
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        $seoProcess = SeoProcess::create([
            'website_id' => $request->website_id,
            'user_id' => $request->user_id,
            'word_count' => $request->word_count,
            'suggestion' => $request->suggestion,
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
        $this->addChecklistData($seoProcess);
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => "SEO Content Added Successfully."
        ]);
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
        $data['moduleName'] = $this->moduleName;
        return view("{$this->view}/view", $data);
    }

    public function edit(Request $request, int $id)
    {      
        $data['seoProcess'] = SeoProcess::find($id);
        $data['storeWebsites'] = StoreWebsite::select('id', 'website')->get();
        $data['seoProcessStatus'] = SeoProcessStatus::all();
        $data['users'] = User::select('id', 'name')->get();
        $data['moduleName'] = $this->moduleName;
        $html = view("{$this->view}/ajax/edit", $data)->render();
        return response()->json([
            'success' => true,
            'data' => $html,
        ]);
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
        $this->addChecklistData($seoProcess);
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => "SEO Content Updated Successfully."
        ]);
    }

    private function addKeywordData(SeoProcess $seoProcess)
    {   
        $request = request();
        if(!empty($request->keyword)) {
            $seoKeyword = [];
            $seoProcess->keywords()->delete();
            foreach($request->keyword as $ky => $keyword) {
                $seoKeyword[] = new SeoProcessKeyword([
                    'name' => $keyword,
                    'index' => $ky + 1,
                ]);
            }
            $seoProcess->keywords()->saveMany($seoKeyword);
        }
    }

    private function addChecklistData(SeoProcess $seoProcess)
    {
        $request = request();
        if(!empty($request->seo_checklist)) {
            $checklist = [];
            $seoProcess->seoChecklist()->delete();
            $cnt = 1;
            foreach($request->seo_checklist as $ky => $item) {
                $checklist[] = new SeoProcessRemark([
                    'seo_process_status_id' => $ky,
                    'remark' => $item,
                    'index' => $cnt,
                ]);
                $cnt++;
            }
            $seoProcess->seoChecklist()->saveMany($checklist);
        }

        if(!empty($request->publish_checklist)) {
            $checklist = [];
            $seoProcess->publishChecklist()->delete();
            $cnt = 1;
            foreach($request->publish_checklist as $ky => $item) {
                $checklist[] = new SeoProcessRemark([
                    'seo_process_status_id' => $ky,
                    'remark' => $item,
                    'index' => $cnt,
                ]);
                $cnt++;
            }
            $seoProcess->publishChecklist()->saveMany($checklist);
        }
    }

    private function getAjaxSeoProcess()
    {
        $auth = auth()->user();
        $request = request();
        $seoProcess = SeoProcess::with(['website:id,website', 'user:id,name']);
        $filter = (object) $request->filter;
        if(!empty($filter->website_id)) {
            $seoProcess = $seoProcess->where('website_id', $filter->website_id);
        }

        if(!empty($filter->price_status)) {
            if($filter->price_status == 1) {
                $seoProcess = $seoProcess->where('is_price_approved', $filter->price_status);
            } else {
                $seoProcess = $seoProcess->where('is_price_approved', 0);
            }
        }

        if(!empty($filter->user_id)) {
            $seoProcess = $seoProcess->where('user_id', $filter->user_id);
        }

        if(!empty($filter->status)) {
            $seoProcess = $seoProcess->where('status', $filter->status);
        }

        if($auth->hasRole(['user'])) {
            $seoProcess = $seoProcess->where('user_id', $auth->id);
        }
        $datatable = datatables()->eloquent($seoProcess)
            ->addColumn('actions', function($val) use($auth) {
                $editUrl = route('seo.content.edit', $val->id);
                $showUrl = route('seo.content.show', $val->id);
                $actions = '';
                if($auth->hasRole(['Admin', 'user', 'Seo Head'])) {
                    $actions .= "<a href='javascript:;' data-url='{$editUrl}' class='btn btn-secondary btn-sm editBtn'>Edit</a>";
                }
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
            ->editColumn('user_id', function($val) {
                return $val->user->name ?? '-';
            })
            ->editColumn('website_id', function($val) {
                return $val->website->website ?? '-';
            })
            ->addColumn('keywords', function($val) {
                if(!empty($val->keywords)) {
                    $keyword = "<ul class='list-group'>";
                    foreach($val->keywords as $item) {
                        $keyword .= "<li class='list-group-item bg-custom-gray'>" . ($item->name ?? '-') . "</li>";
                    }
                    $keyword .= "</ul>";
                    return $keyword;
                }
                return '-';
            })
            ->addColumn('seoChecklist', function($val) {
                $checkList = "<ul class='list-group'>";
                foreach($val->seoChecklist as $item) {
                    $checkList .= "<li class='list-group-item bg-custom-gray'>" . "<b>" . ($item->processStatus->label ?? '-') . "</b>" . "<br>" . ($item->remark ?? '-') . "</li>";
                }
                $checkList .= "</ul>";
                return $checkList;
            })
            ->addColumn('publishChecklist', function($val) {
                $checkList = "<ul class='list-group'>";
                foreach($val->publishChecklist as $item) {
                    $checkList .= "<li class='list-group-item bg-custom-gray'>" . "<b>" . ($item->processStatus->label ?? '-') . "</b>" . "<br>" . ($item->remark ?? '-') . "</li>";
                }
                $checkList .= "</ul>";
                return $checkList;
            })
            ->addColumn('documentLink', function($val) {
                return "<a target='_blank' href='{$val->google_doc_link}'>{$val->google_doc_link}</a>";
            })
            ->addColumn('liveStatusLink', function($val) {
                return "<a target='_blank' href='{$val->google_doc_link}'>{$val->google_doc_link}</a>";
            })
            ->editColumn('price', function($val) use($auth) {
                $price = "<b>{$val->price}</b>";
                if($auth->hasRole(['Admin'])) {
                    if($val->is_price_approved) {
                        $price .= "<br> <i>Approved</i>";
                    }
                }
                return $price;
            })
            ->addColumn('seoStatus', function($val) {
                return $val->seoStatus->label ?? '-';
            })
            ->rawColumns(['actions', 'status', 'keywords', 'seoChecklist', 'publishChecklist', 'documentLink', 'liveStatusLink', 'price'])
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
