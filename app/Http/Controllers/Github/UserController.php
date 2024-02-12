<?php

namespace App\Http\Controllers\Github;

use App\User;
use GuzzleHttp\Client;
use App\Github\GithubUser;
use Illuminate\Http\Request;
use GuzzleHttp\RequestOptions;
use App\Github\GithubRepository;
use App\Github\GithubRepositoryUser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

class UserController extends Controller
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'auth' => [config('env.GITHUB_USERNAME'), config('env.GITHUB_TOKEN')],
        ]);
    }

    private function connectGithubClient($userName, $token)
    {
        $githubClient = new Client([
            'auth' => [$userName, $token],
        ]);

        return $githubClient;
    }

    public function listOrganizationUsers()
    {
        $platformUsers = User::all(['id', 'name', 'email']);

        $users = GithubUser::with('repositories', 'platformUser')->get();

        return view(
            'github.org_users',
            [
                'users' => $users,
                'platformUsers' => $platformUsers,
            ]
        );
    }

    public function listUsersOfRepository($repoId)
    {
        $githubRepository = GithubRepository::with('users')->where('id', $repoId)->first();
        $users = $githubRepository->users;

        return view(
            'github.repository_users',
            [
                'users' => $users,
                'repoId' => $repoId,
                'githubRepository' => $githubRepository,
            ]
        );
    }

    public function linkUser(Request $request)
    {
        $bodyContent = $request->getContent();
        $jsonDecodedBody = json_decode($bodyContent);

        $userId = $jsonDecodedBody->user_id;
        $githubUserId = $jsonDecodedBody->github_user_id;

        if (! $userId || ! $githubUserId) {
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
        $repoName = $jsonDecodedBody->repository_name;

        if (! $userName || ! $access || ! $repoName) {
            return response()->json(
                [
                    'error' => 'Missing parameters',
                ],
                400
            );
        }

        //https://api.github.com/repos/:owner/:repo/collaborators/:username
        $url = 'https://api.github.com/repos/' . config('env.GITHUB_ORG_ID') . '/' . $repoName . '/collaborators/' . $userName;

        // cannot update users access directly and hence need to remove and then add them explicitly
        $this->client->delete($url);
        $this->client->put(
            $url,
            [
                RequestOptions::JSON => [
                    'permission' => $access,
                ],
            ]
        );

        return response()->json([
            'message' => 'user invited',
        ]);
    }

    public function removeUserFromRepository()
    {
        $id = Route::current()->parameter('id');

        $repositoryUser = GithubRepositoryUser::find($id);

        $user = $repositoryUser->githubUser;
        $repository = $repositoryUser->githubRepository;
        $organization = $repository->organization;

        $url = 'https://api.github.com/repos/' . $organization->name . '/' . $repository->name . '/collaborators/' . $user->username;

        $githubClient = $this->connectGithubClient($organization->username, $organization->token);

        $githubClient->delete($url);

        $repositoryUser->delete();

        return redirect()->back();
    }

    public function userDetails()
    {
        $id = Route::current()->parameter('userId');

        $userDetails = GithubUser::getUserDetails($id);

        return view('github.user_details', ['userDetails' => $userDetails]);
    }

    public function addUserToRepositoryForm($repoId)
    {
        $repositoryName = Route::current()->parameter('name');

        $githubUsers = GithubUser::all();

        $users = [];
        foreach ($githubUsers as $user) {
            $users[$user->username] = $user->username;
        }

        return view('github.add_user_to_repo', ['repoId' => $repoId, 'users' => $users]);
    }

    public function addUserToRepository(Request $request)
    {
        $repoId = $request->repoId;
        $username = $request->username;
        $permission = $request->permission;

        $githubRepository = GithubRepository::find($repoId);
        $organization = $githubRepository->organization;

        //https://api.github.com/repos/:owner/:repo/collaborators/:username
        $url = 'https://api.github.com/repos/' . $organization->name . '/' . $githubRepository->name . '/collaborators/' . $username;

        $githubClient = $this->connectGithubClient($organization->username, $organization->token);

        $githubClient->put(
            $url,
            [
                RequestOptions::JSON => [
                    'permission' => $permission,
                ],
            ]
        );

        // cannot update the database still as the above will raise and invitation

        return redirect('/github/repos/' . $repoId . '/users');
    }
}
