<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\KeywordSearchVariants;
use Yajra\DataTables\DataTables;

class KeywordVariantController extends Controller
{

    public function create(Request $request)
    {
        if ($request->ajax()) {
            $variant = new KeywordSearchVariants();
            $variant->keyword = $request->keyword;
            $variant->save();
            return response()->json(['message' => "Variant add successfully"]);
        }
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = KeywordSearchVariants::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function delete(Request $request, $id)
    {

        $variant = KeywordSearchVariants::find($id);

        if ($variant) {
            $variant->delete();
        }

        return response()->json(['message' => 'delete successfully']);
    }
}
