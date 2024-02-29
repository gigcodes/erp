<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use App\EnvDescription;
use Illuminate\Http\Request;
use Brotzka\DotenvEditor\DotenvEditor;

class EnvController extends Controller
{
    public function loadEnvManager()
    {
        $env = new DotenvEditor();

        return view('env_manager.overview-adminlte');
    }

    public function addEnv(Request $request)
    {
        $env      = new DotenvEditor();
        $client   = new Client();
        $server   = env('APP_ENV');
        $url      = $server === 'production' ? 'https://erpstage.theluxuryunlimited.com/api/add-env' : 'https://erp.theluxuryunlimited.com/api/add-env';
        $response = [];
        if ($request->get('addToLive') && $request->get('addToLive') === '1') {
            $response = $client->request('POST', $url, [
                'form_params' => [
                    'key'    => $request->get('key'),
                    'value'  => $request->get('value'),
                    '_token' => $request->get('_token'),
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
        $envDescription              = new EnvDescription();
        $envDescription->key         = $request->get('key');
        $envDescription->description = $request->get('description');
        $envDescription->save();

        return response()->json($response);
    }

    public function getDescription()
    {
        $envDescription = EnvDescription::all()->toArray();

        return response()->json($envDescription);
    }

    public function editEnv(Request $request)
    {
        $client = new Client();
        $env    = new DotenvEditor();

        $server   = env('APP_ENV');
        $url      = $server === 'production' ? 'https://erpstage.theluxuryunlimited.com/api/edit-env' : 'https://erp.theluxuryunlimited.com/api/edit-env';
        $response = [];
        if ($request->get('server') === 'production' || $request->get('server') === 'staging') {
            $response = $client->request('POST', $url, [
                'form_params' => [
                    'key'         => $request->get('key'),
                    'value'       => $request->get('value'),
                    'description' => $request->get('description'),
                    '_token'      => $request->get('_token'),
                ],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);
            $response = (string) $response->getBody()->getContents();
            $response = json_decode($response, true);
        }
        // Changes the value of the Database name and username
        EnvDescription::where('key', $request->get('key'))->updateOrCreate(['key' => $request->get('key'), 'description' => $request->get('description')]);

        $env->changeEnv([
            $request->get('key') => $request->get('value'),
        ]);

        return response()->json($response);
    }
}
