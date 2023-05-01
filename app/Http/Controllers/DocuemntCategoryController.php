<?php

namespace App\Http\Controllers;

use Response;
use App\DocumentCategory;
use Illuminate\Http\Request;

class DocuemntCategoryController extends Controller
{
    public function addCategory(Request $request)
    {
        $category = new DocumentCategory;

        $category->name = $request->name;

        $category->save();

        if ($category->id != null) {
            return Response::json([
                'success' => true,
                'message' => 'Category Created Sucessfully',
            ]);
        } else {
            return Response::json([
                'success' => false,
                'message' => 'Category Not Created',
            ]);
        }
    }
}
