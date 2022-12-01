<?php

namespace App\Http\Controllers;

class EnvController extends Controller
{
    public function loadEnvManager()
    {
        return view('env_manager.overview-adminlte');
    }
}
