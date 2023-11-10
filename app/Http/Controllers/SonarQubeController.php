<?php

namespace App\Http\Controllers;

use App\Models\SonarQube;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SonarQubeController extends Controller
{
    public function createProject(Request $request)
    {
        $url = env('SONARQUBE_URL') . 'api/projects/create';

        try {
            $response = Http::withBasicAuth(env('SONARQUBE_USERNAME'), env('SONARQUBE_PASSWORD'))
                ->post($url, [
                    'project' => $request->project,
                    'name' => $request->name,
                ]);

            if ($response->status() == 200) {
                return response()->json(['code' => 200, 'data' => $response, 'message' => 'Project created successfully!']);
            } else {
                return response()->json(['code' => 400, 'data' => $response, 'message' => 'Project Name Already exixts!']);
            }
        } catch (\Illuminate\Http\Client\RequestException $exception) {
            $response = $exception->response;
            if ($response->status() === 400 && strpos($response->body(), 'Project already exists')) {
                return response()->json(['code' => 400, 'message' => 'Project with the same name already exists.']);
            }

            return response()->json(['code' => 500, 'message' => 'An error occurred while creating the project.']);
        }
    }

    public function searchProject(Request $request)
    {
        $url = env('SONARQUBE_URL') . 'api/projects/search';

        $response = Http::withBasicAuth(env('SONARQUBE_USERNAME'), env('SONARQUBE_PASSWORD'))
            ->get($url, [
                'project' => 'brands-labels',
            ]);

        $responseData = $response->json();

        $html = view('sonarCube.project-list-modal-html')->with('projects', $responseData)->render();

        return response()->json(['code' => 200, 'data' => $responseData, 'html' => $html, 'message' => 'Content render']);
    }

    public function searchIssues(Request $request)
    {
        /*$url = env('SONARQUBE_URL') . 'api/issues/search';

        $statuses = $request->query('statuses');
        $types = $request->query('types');
        $ps = $request->query('ps');
        $p = $request->query('p');
        $components = $request->query('components');

        $queryParams = [
            'statuses' => $statuses,
            'types' => $types,
            'ps' => $ps,
            'p' => $p,
            'components' => $components,
        ];

        $username = env('SONARQUBE_USERNAME');
        $password = env('SONARQUBE_PASSWORD');

        $response = Http::withBasicAuth($username, $password)
            ->get($url, $queryParams);

        $responseData = $response->json();

        return view('sonarCube.index', ['issues' => $responseData]);*/

        $issues = SonarQube::orderBy("id", "DESC")->paginate(100);

        return view('sonarCube.index', compact('issues'))->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function searchUserTokens(Request $request)
    {
        $url = env('SONARQUBE_URL') . 'api/user_tokens/search';

        $response = Http::withBasicAuth(env('SONARQUBE_USERNAME'), env('SONARQUBE_PASSWORD'))
            ->get($url);

        $responseData = $response->json();

        $html = view('sonarCube.project-user-list-modal-html')->with('projects', $responseData)->render();

        return response()->json(['code' => 200, 'data' => $responseData, 'html' => $html, 'message' => 'Content render']);
    }
}
