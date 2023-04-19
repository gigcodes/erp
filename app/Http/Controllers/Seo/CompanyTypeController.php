<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Models\Seo\SeoCompanyType;
use CreateSeoCompanyTypeTbl;
use Illuminate\Http\Request;

class CompanyTypeController extends Controller
{
    public function index()
    {
        
    }

    public function store(Request $request)
    {
        $seoCompany = SeoCompanyType::query()->where('name', trim($request->name));
        if($seoCompany->count() < 1) {
            $seoCompany = SeoCompanyType::create([
                'name' => $request->name,
            ]);
            return response()->json([
                'success' => true,
                'data' => $seoCompany,
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $seoCompany->first()
        ]);
    }
}
