<?php

namespace App\Http\Controllers;

use App\Brand;
use App\GmailData;
use Illuminate\Http\Request;

class GmailDataController extends Controller
{
    public function index() {
        $data = GmailData::all();

        $brands = Brand::get()->pluck('name')->toArray();

        return view('scrap.gmail', compact('brands', 'data'));
    }
}
