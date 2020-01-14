<?php

namespace App\Http\Controllers\Github;

use App\Github\GithubRepository;
use App\Github\GithubRepositoryUser;
use App\Github\GithubUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use GuzzleHttp\Client;
use Route;

class UserController extends Controller
{

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
        if($githubUser){
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
}
