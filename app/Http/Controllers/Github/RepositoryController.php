<?php

namespace App\Http\Controllers\Github;

use App\Github\GithubRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class RepositoryController extends Controller
{
    private $client;

    function __construct()
    {
        $this->client = new Client([
            'auth' => [getenv('GITHUB_USERNAME'), getenv('GITHUB_TOKEN')]
        ]);
    }

    private function refreshGithubRepos()
    {
        $url = "https://api.github.com/orgs/" . getenv('GITHUB_ORG_ID') . "/repos";
        $response = $this->client->get($url);

        $repositories = json_decode($response->getBody()->getContents());

        $dbRepositories = [];

        foreach ($repositories as $repository) {

            $data = [
                'id' => $repository->id,
                'name' => $repository->name,
                'html' => $repository->html_url,
                'webhook' => $repository->hooks_url,
                'created_at' => Carbon::createFromFormat(DateTime::ISO8601, $repository->created_at),
                'updated_at' => Carbon::createFromFormat(DateTime::ISO8601, $repository->updated_at)
            ];

            GithubRepository::updateOrCreate(
                [
                    'id' => $repository->id
                ],
                $data
            );
            $dbRepositories[] = $data;
        }
        return $dbRepositories;
    }

    //
    public function listRepositories()
    {
        $repositories = $this->refreshGithubRepos();
        return view('github.repositories', [
            'repositories' => $repositories
        ]);
    }
}
