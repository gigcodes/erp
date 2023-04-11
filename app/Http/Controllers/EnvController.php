<?php

namespace App\Http\Controllers;

use Brotzka\DotenvEditor\DotenvEditor;
use Illuminate\Http\Request;

class EnvController extends Controller
{

    public function loadEnvManager()
    {
        $env = new DotenvEditor();
        $output = $env->getContent();
        return view('env_manager.overview-adminlte');
    }
    public function addEnv(Request $request)
    {
        $env = new DotenvEditor();
        $env->addData([
            $request->get('key') => $request->get('value'),
        ]);
        return response()->json(['code' => 200, 'message' => 'Key added successfully']);
    }
}
