<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seo\SeoCompanyType;
use App\EmailAddress;
use App\Models\Seo\SeoCompanyHistroy;
use App\Models\Seo\SeoCompany;
use App\StoreWebsite;
use App\User;
use DB;
use Illuminate\Support\Facades\Redirect;

class CompanyController extends Controller
{
    private $route = "seo.company";
    private $view = "seo/company";
    private $moduleName = "SEO Company";

    public function index(Request $request)
    {
        if($request->ajax()) {
            if($request->type == 'COMPANY_HISTORY') {
                return $this->historyTable();
            }
            return $this->tableData();
        }

        $data['websites'] = StoreWebsite::select('id', 'website')->get();
        $data['users'] = User::select('id', 'name')->get();
        $data['companyTypes'] = SeoCompanyType::all();
        $data['moduleName'] = $this->moduleName;
        return view("{$this->view}/index", $data);
    }

    public function create()
    {
        $data['companyTypes'] = SeoCompanyType::all();
        $data['webistes'] = StoreWebsite::all();
        $data['emailAddresses'] = EmailAddress::select('id', 'username', 'password')->get();
        $data['moduleName'] = $this->moduleName;
        $html =  view("{$this->view}/ajax/create", $data)->render();
        return response()->json([
            'success' => true,
            'title' => "Add SEO Company",
            'data' => $html
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        $seoCompany = SeoCompany::create([
            'user_id' => auth()->id(),
            'company_type_id' => $request->type_id,
            'website_id' => $request->website_id,
            'da' => $request->da,
            'pa' => $request->pa,
            'ss' => $request->ss,
            'email_address_id' => $request->email_address_id,
            'live_link' => $request->live_link,
            'status' => $request->status,
        ]);
        $this->addHistory($seoCompany);
        DB::commit();

        return response()->json([
            'success' => true,
        ]);
    }

    public function edit(Request $request, int $id)
    {
        $data['companyTypes'] = SeoCompanyType::all();
        $data['webistes'] = StoreWebsite::all();
        $data['emailAddresses'] = EmailAddress::select('id', 'username', 'password')->get();
        $data['moduleName'] = $this->moduleName;
        $data['seoCompany'] = SeoCompany::findOrFail($id);
        $html = view("{$this->view}/ajax/edit", $data)->render();
        return response()->json([
            'success' => true,
            'title' => "Edit SEO Company",
            'data' => $html
        ]);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();
        $seoCompany = SeoCompany::findOrFail($id);
        $seoCompany->update([
            'company_type_id' => $request->type_id,
            'website_id' => $request->website_id,
            'da' => $request->da,
            'pa' => $request->pa,
            'ss' => $request->ss,
            'email_address_id' => $request->email_address_id,
            'live_link' => $request->live_link,
            'status' => $request->status,
        ]);
        $this->addHistory($seoCompany);
        DB::commit();

        return response()->json([
            'success' => true,
        ]);
    }

    private function tableData()
    {
        $request = request();
        
        $seoCompanies = SeoCompany::with(['website:id,website', 'user:id,name']);

        if(!empty($request->search['value'])) {
            $searchVal = $request->search['value'];
            $seoCompanies = $seoCompanies->where(function($query) use ($searchVal) {
                $query->where('da', 'LIKE', "%$searchVal%")
                    ->orWhere('pa', 'LIKE', "%$searchVal%")
                    ->orWhere('ss', 'LIKE', "%$searchVal%")
                    ->orWhere('live_link', 'LIKE', "%$searchVal%");
            });
        }

        if(!empty($request->companyTypeId)) {
            $seoCompanies = $seoCompanies->where('company_type_id', $request->companyTypeId);
        }
        if(!empty($request->websiteId)) {
            $seoCompanies = $seoCompanies->where('website_id', $request->websiteId);
        }
        if(!empty($request->userId)) {
            $seoCompanies = $seoCompanies->where('user_id', $request->userId);
        }

        if(!empty($request->status)) {
            $seoCompanies = $seoCompanies->where('status', $request->status);
        }
        
        return datatables()->eloquent($seoCompanies)
            ->addColumn('actions', function($val) {
                $editUrl = route('seo.company.edit', $val->id);
                $actions = '';
                $actions .= "<a href='javascript:;' data-url='{$editUrl}' class='btn btn-sm btn-secondary mr-1 editBtn'>Edit</a>";
                $actions .= "<a href='javascript:;' data-id='{$val->id}' class='btn btn-sm btn-secondary historyBtn'>History</a>";
                return $actions;
            })
            ->editColumn('created_at', function($val) {
                return date('Y-m-d h:i A', strtotime($val->created_at));
            })
            ->addColumn('company', function($val) {
                return $val->companyType->name ?? '-';
            })
            ->addColumn('username', function($val) {
                return $val->emailAddress->username ?? '-';
            })
            ->addColumn('password', function($val) {
                return $val->emailAddress->password ?? '-';
            })
            ->addColumn('liveLink', function($val) {
                return "<a target='_blank' href='$val->live_link'>{$val->live_link}</a>";
            })
            ->addColumn('status', function($val) {
                $val->status = ucfirst($val->status);
                return "<span class='badge'>{$val->status}</a>";
            })
            ->addIndexColumn()
            ->rawColumns(['actions', 'liveLink', 'status'])
            ->make(true);
    }

    private function historyTable()
    {
        $request = request();
        $seoCompanies = SeoCompanyHistroy::with(['website:id,website', 'user:id,name'])->where('seo_company_id', $request->companyId);
        return datatables()->eloquent($seoCompanies)
            ->editColumn('created_at', function($val) {
                return date('Y-m-d h:i A', strtotime($val->created_at));
            })
            ->addColumn('company', function($val) {
                return $val->companyType->name ?? '-';
            })
            ->addColumn('company', function($val) {
                return $val->companyType->name ?? '-';
            })
            ->addColumn('username', function($val) {
                return $val->emailAddress->username ?? '-';
            })
            ->addColumn('password', function($val) {
                return $val->emailAddress->password ?? '-';
            })
            ->addColumn('liveLink', function($val) {
                return "<a target='_blank' href='$val->live_link'>{$val->live_link}</a>";
            })
            ->addColumn('status', function($val) {
                $val->status = ucfirst($val->status);
                return "<span class='badge'>{$val->status}</a>";
            })
            ->addIndexColumn()
            ->rawColumns(['actions', 'liveLink', 'status'])
            ->make(true);
    }
    
    private function addHistory(SeoCompany $seoCompany): bool
    {
        $request = request();
        SeoCompanyHistroy::create([
            'seo_company_id' => $seoCompany->id,
            'user_id' => auth()->id(),
            'company_type_id' => $request->type_id,
            'website_id' => $request->website_id,
            'da' => $request->da,
            'pa' => $request->pa,
            'ss' => $request->ss,
            'email_address_id' => $request->email_address_id,
            'live_link' => $request->live_link,
            'status' => $request->status,
        ]);

        return true;
    }

}
