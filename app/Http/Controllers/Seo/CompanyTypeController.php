<?php

namespace App\Http\Controllers\Seo;

use Illuminate\Http\Request;
use App\Models\Seo\SeoCompanyType;
use App\Http\Controllers\Controller;

class CompanyTypeController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $types = SeoCompanyType::query();

            return datatables()->eloquent($types)
                ->addColumn('actions', function ($val) {
                    $editUrl   = route('seo.company-type.edit', $val->id);
                    $deleteUrl = route('seo.company-type.destroy', $val->id);
                    $actions   = '';
                    $actions .= "<a href='javascript:;' data-url='{$editUrl}' class='btn btn-secondary btn-sm typeEditBtn'>Edit</a>";
                    $actions .= "<a href='javascript:;' data-url='{$deleteUrl}' class='btn btn-secondary btn-sm ml-2 typeDeleteBtn'>Delete</a>";

                    return $actions;
                })
                ->addIndexColumn()
                ->rawColumns(['actions'])
                ->make(true);
        }
    }

    public function create()
    {
        $data['submitUrl'] = route('seo.company-type.store');
        $html              = view('seo.company.ajax.typeForm', $data)->render();

        return response()->json([
            'success' => true,
            'title'   => 'Add company type',
            'data'    => $html,
        ]);
    }

    public function edit(int $id)
    {
        $data['submitUrl']  = route('seo.company-type.update', $id);
        $data['seoCompany'] = SeoCompanyType::find($id);
        $html               = view('seo.company.ajax.typeForm', $data)->render();

        return response()->json([
            'success' => true,
            'title'   => 'Edit company type',
            'data'    => $html,
        ]);
    }

    public function store(Request $request)
    {
        $seoCompany = SeoCompanyType::query()->where('name', trim($request->name));
        if ($seoCompany->count() < 1) {
            $seoCompany = SeoCompanyType::create([
                'name' => $request->name,
            ]);

            return response()->json([
                'success' => true,
                'data'    => $seoCompany,
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => $seoCompany->first(),
        ]);
    }

    public function update(int $id, Request $request)
    {
        $seoCompany = SeoCompanyType::find($id)->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

    public function destroy(int $id)
    {
        SeoCompanyType::find($id)->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
