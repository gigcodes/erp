<?php

namespace App\Http\Controllers\Github;

use App\Github\GithubRepository;
use App\Github\GithubRepositoryUser;
use App\Github\GithubUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Route;

class UserController extends Controller
{

    private $client;
    function __construct()
    {
        $this->client = new Client([
            'auth' => [getenv('GITHUB_USERNAME'), getenv('GITHUB_TOKEN')]
        ]);
    }

    public function listOrganizationUsers()
    {
        $platformUsers = User::all(['id', 'name', 'email']);

        $users = GithubUser::with('repositories', 'platformUser')->get();
        return view(
            'github.org_users',
            [
                'users' => $users,
                'platformUsers' => $platformUsers
            ]
        );
    }

    public function listUsersOfRepository()
    {
        $name = Route::current()->parameter('name');
        //$users = $this->refreshUsersForRespository($name);
        $users = GithubRepository::where('name', $name)->first()->users;
        return view(
            'github.repository_users',
            [
                'users' => $users,
                'repoName' => $name
            ]
        );
    }

    public function linkUser(Request $request)
    {
        $bodyContent = $request->getContent();
        $jsonDecodedBody = json_decode($bodyContent);

        $userId = $jsonDecodedBody->user_id;
        $githubUserId = $jsonDecodedBody->github_user_id;

        if (!$userId || !$githubUserId) {
            return response()->json(
                [
                    'error' => 'Missing parameters',
                ],
                400
            );
        }

        $githubUser = GithubUser::find($githubUserId);
        if ($githubUser) {
            $githubUser->user_id = $userId;
            $githubUser->save();
            return response()->json(
                [
                    'message' => 'Saved user',
                ]
            );
        }

        return response()->json(
            [
                'error' => 'Unable to find user',
            ],
            404
        );
    }

    public function modifyUserAccess(Request $request)
    {
        $bodyContent = $request->getContent();
        $jsonDecodedBody = json_decode($bodyContent);

        $userName = $jsonDecodedBody->user_name;
        $access = $jsonDecodedBody->access;
        $repoName  = $jsonDecodedBody->repository_name;



        if (!$userName || !$access || !$repoName) {
            return response()->json(
                [
                    'error' => 'Missing parameters',
                ],
                400
            );
        }

        //https://api.github.com/repos/:owner/:repo/collaborators/:username
        $url = "https://api.github.com/repos/" . getenv('GITHUB_ORG_ID')  . "/" . $repoName . "/collaborators/" . $userName;

        // cannot update users access directly and hence need to remove and then add them explicitly
        $this->client->delete($url);
        $this->client->put(
            $url,
            [
                RequestOptions::JSON => [
                    'permission' => $access
                ]
            ]
        );
        return response()->json([
            'message' => 'user invited'
        ]);
    }

    public function removeUserFromRepository(){

        $name = Route::current()->parameter('name');
        $username = Route::current()->parameter('username');

        print_r($name);
        print_r($username);
        exit;

        $url = "https://api.github.com/repos/" . getenv('GITHUB_ORG_ID')  . "/" . $name . "/collaborators/" . $username;
        $this->client->delete($url);

        return redirect('/github/repos/'.$name.'/users');
    }
}
