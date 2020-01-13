<?php

namespace App\Http\Controllers\Github;

use App\Github\GithubGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;

class GroupController extends Controller
{

    private $client;

    function __construct()
    {
        $this->client = new Client([
            'auth' => [getenv('GITHUB_USERNAME'), getenv('GITHUB_TOKEN')]
        ]);
    }

    private function refreshGithubGroups()
    {
        $url = "https://api.github.com/orgs/" . getenv('GITHUB_ORG_ID') . "/teams";
        $response = $this->client->get($url);

        $groups = json_decode($response->getBody()->getContents());
        $returnGroup  = [];
        foreach ($groups as $group) {
            $dbGroup = [
                'id' => $group->id,
                'name' => $group->name
            ];

            GithubGroup::updateOrCreate(
                ['id' => $group->id],
                $dbGroup
            );

            $returnGroup[] = $dbGroup;
        }
        return $returnGroup;
    
    }

    //
    public function listGroups()
    {
        $groups = $this->refreshGithubGroups();
        return view('github.groups', ['groups' => $groups]);
    }
}
