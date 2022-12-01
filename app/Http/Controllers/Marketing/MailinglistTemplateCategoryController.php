<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMailingListTemplateCategoryRequest;
use App\MailinglistTemplate;
use App\MailinglistTemplateCategory;
use App\StoreWebsite;

class MailinglistTemplateCategoryController extends Controller
{
    public function store(StoreMailingListTemplateCategoryRequest $request)
    {
        $logged_user = $request->user();

        $cid = MailinglistTemplateCategory::create([
            'title' => $request->name,
            'user_id' => $logged_user->id,
        ]);
        $storeWebSites = StoreWebsite::get();
        $data = [
            'store_website_id' => 0,
            'category_id' => $cid->id,
        ];
        MailinglistTemplate::insert($data);
        foreach ($storeWebSites as $s) {
            $data = [
                'store_website_id' => $s->id,
                'category_id' => $cid->id,
            ];
            MailinglistTemplate::insert($data);
        }

        return response()->json([
            'status' => true,
        ]);
    }
}
