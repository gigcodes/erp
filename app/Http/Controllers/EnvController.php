<?php

namespace App\Http\Controllers;

use Brotzka\DotenvEditor\DotenvEditor;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EnvController extends Controller
{

    public function loadEnvManager()
    {
        $env = new DotenvEditor();
        return view('env_manager.overview-adminlte');
    }
    public function addEnv(Request $request)
    {
        $env = new DotenvEditor();
        $client = new Client();
        $response = [];
        if($request->get('addToLive') && $request->get('addToLive') === '1'){
            $response = $client->request('POST', 'https://erp.theluxuryunlimited.com/api/add-env', [
                "form_params" => [
                    "key" => $request->get('key'),
                    "value" => $request->get('value'),
                    "_token" => $request->get('_token')
                ],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);
            $response = (string) $response->getBody()->getContents();
            $response = json_decode($response, true);

        }
        $env->addData([
            $request->get('key') => $request->get('value'),
        ]);
        return response()->json($response);
    }
}
