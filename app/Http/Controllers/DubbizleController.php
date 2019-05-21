<?php

namespace App\Http\Controllers;

use App\Dubbizle;
use Illuminate\Http\Request;

class DubbizleController extends Controller
{
    public function index() {
        $posts = Dubbizle::all();

        return view('dubbizle', compact('posts'));
    }
}
