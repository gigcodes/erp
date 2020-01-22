<?php

namespace App\Http\Controllers\Github;

use App\Github\GithubRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DateTime;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Input;
use Twilio\TwiML\Messaging\Body;

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

    public function getRepositoryDetails($repositoryId)
    {
        $repository = GithubRepository::find($repositoryId);
        $branches = $repository->branches;

        $currentBranch = exec('sh '.getenv('DEPLOYMENT_SCRIPTS_PATH').'erp/get_current_deployment.sh');
        
       //exec('sh '.getenv('DEPLOYMENT_SCRIPTS_PATH').'erp/deploy_branch.sh master');
        
        //exit;
        return view('github.repository_settings', [
            'repository' => $repository,
            'branches' => $branches,
            'current_branch' => $currentBranch
        ]);



        //print_r($repository);
    }

    public function mergeBranch($id)
    {

        $source = Input::get('source');
        $destination = Input::get('destination');

        $url = "https://api.github.com/repositories/" . $id . "/merges";

        try {
            $this->client->post(
                $url,
                [
                    RequestOptions::BODY => json_encode([
                        'base' => $destination,
                        'head' => $source,
                    ])
                ]
            );
            echo 'done';
        } catch (Exception $e) {
            print_r($e->getMessage());
            return redirect(url('/github/repos/' . $id . '/branches'))->with(
                [
                    'message' => 'Failed to Merge!',
                    'alert-type' => 'error'
                ]
            );
            
        }
        return redirect(url('/github/repos/' . $id . '/branches'))->with([
            'message' => 'Branch merged successfully',
            'alert-type' => 'success'
        ]);
    }
}
