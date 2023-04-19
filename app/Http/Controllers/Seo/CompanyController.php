<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seo\SeoCompanyType;
use App\EmailAddress;
use App\Models\Seo\SeoCompanyHistroy;
use App\Models\Seo\SeoCompany;
use App\StoreWebsite;
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
        return view("{$this->view}/create", $data);
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
            'live_link' => $request->live_link
        ]);
        $this->addHistory($seoCompany);
        DB::commit();

        return Redirect::route("{$this->route}.index");
    }

    public function edit(Request $request, int $id)
    {
        $data['companyTypes'] = SeoCompanyType::all();
        $data['webistes'] = StoreWebsite::all();
        $data['emailAddresses'] = EmailAddress::select('id', 'username', 'password')->get();
        $data['moduleName'] = $this->moduleName;
        $data['seoCompany'] = SeoCompany::findOrFail($id);
        return view("{$this->view}/edit", $data);
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
            'live_link' => $request->live_link
        ]);
        $this->addHistory($seoCompany);
        DB::commit();

        return Redirect::route("{$this->route}.index");
    }

    private function tableData()
    {
        $request = request();
        $seoCompanies = SeoCompany::with(['website:id,website', 'user:id,name']);
        if(!empty($request->companyTypeId)) {
            $seoCompanies = $seoCompanies->where('company_type_id', $request->companyTypeId);
        }
        return datatables()->eloquent($seoCompanies)
            ->addColumn('actions', function($val) {
                $editUrl = route('seo.company.edit', $val->id);
                $actions = '';
                $actions .= "<a href='{$editUrl}' class='btn btn-sm btn-secondary mr-1'>Edit</a>";
                $actions .= "<a href='javascript:;' data-id='{$val->id}' class='btn btn-sm btn-secondary historyBtn'>History</a>";
                return $actions;
            })
            ->editColumn('created_at', function($val) {
                return date('Y-m-d H:i:s', strtotime($val->created_at));
            })
            ->addIndexColumn()
            ->rawColumns(['actions'])
            ->make(true);
    }

    private function historyTable()
    {
        $request = request();
        $seoCompanies = SeoCompanyHistroy::with(['website:id,website', 'user:id,name'])->where('seo_company_id', $request->companyId);
        return datatables()->eloquent($seoCompanies)
            ->editColumn('created_at', function($val) {
                return date('Y-m-d H:i:s', strtotime($val->created_at));
            })
            ->editColumn('company_name', function($val) {
                return $val->companyType->name ?? '-';
            })
            ->editColumn('live_link', function($val) {
                if(empty($val->live_link)) {
                    return '-';
                }
                return "<a target='_blank' href='{$val->live_link}'>{$val->live_link}</a>";
            })
            ->addIndexColumn()
            ->rawColumns(['live_link'])
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
            'live_link' => $request->live_link
        ]);

        return true;
    }

}
