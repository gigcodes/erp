<?php

namespace App\Http\Controllers\Github;

use App\Github\GithubGroup;
use App\Github\GithubGroupMember;
use App\Github\GithubRepository;
use App\Github\GithubRepositoryGroup;
use App\Github\GithubUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Input;
use Route;

class GroupController extends Controller
{

    private $client;

    function __construct()
    {
        $this->client = new Client([
            'auth' => [getenv('GITHUB_USERNAME'), getenv('GITHUB_TOKEN')]
        ]);
    }

    public function listGroups()
    {
        $groups = GithubGroup::with('users')->get();
        return view('github.groups', ['groups' => $groups]);
    }

    public function groupDetails()
    {
        $groupId = Route::current()->parameter('groupId');
        $group = GithubGroup::find($groupId);
        $repositories = $group->repositories;
        $users = $group->users;

        return view(
            'github.group_details',
            [
                'group' => $group,
                'repositories' => $repositories,
                'users' => $users
            ]
        );
    }

    public function addRepositoryForm($groupId)
    {
        $group = GithubGroup::find($groupId);
        $existingRepositories = $group->repositories;

        $repositoryIds = $existingRepositories->map(function ($repository) {
            return $repository->id;
        });

        $repositories = GithubRepository::whereNotIn($repositoryIds)->get();
    }

    public function addUserForm($groupId)
    {
        $group = GithubGroup::find($groupId);
        $existingUsers = $group->users;

        $userIds = $existingUsers->map(function ($repository) {
            return $repository->id;
        });

        $users = GithubUser::whereNotIn('id', $userIds)->get(['username']);

        $userSelect = [];
        foreach ($users as $user) {
            $userSelect[$user->username] = $user->username;
        }

        return view(
            'github.group_add_user',
            [
                'group' => $group,
                'users' => $userSelect
            ]
        );
    }

    public function addUser(Request $request)
    {

        $validatedData = $request->validate([
            'group_id' => 'required',
            'role' => 'required',
            'username' => 'required'
        ]);

        $groupId = Input::get('group_id');
        $role = Input::get('role');
        $username = Input::get('username');

        $this->addUserToGroup($groupId, $username, $role);
        return redirect()->back();
    }

    private function addUserToGroup($groupId, $username, $role)
    {
        // https://api.github.com/orgs/:org/teams/:team_slug/memberships/:username
        $url = "https://api.github.com/orgs/" . getenv('GITHUB_ORG_ID') . "/teams/" . $groupId . "/memberships/". $username;
        
        try{
            $response = $this->client->put($url);
            return true;
        }catch(ClientException $e){

        }
        return false;
    }

    public function removeUsersFromGroup()
    {
        $groupId = Route::current()->parameter('groupId');
        $userId = Route::current()->parameter('userId');

        $githubUser = GithubUser::find($userId);

        //https://api.github.com/teams/:team_id/memberships/:username
        $url = 'https://api.github.com/teams/' . $groupId . '/memberships/' . $githubUser->username;
        $this->client->delete($url);

        GithubGroupMember::where('github_groups_id', $groupId)->where('github_users_id', $userId)->delete();

        return redirect()->back();
    }

    public function removeRepositoryFromGroup()
    {
        $groupId = Route::current()->parameter('groupId');
        $repoId = Route::current()->parameter('repoId');

        $repo = GithubRepository::find($repoId);

        //https://api.github.com/teams/:team_id/repos/:owner/:repo
        $url = 'https://api.github.com/teams/' . $groupId . '/repos/' . getenv('GITHUB_ORG_ID') . '/' . $repo->name;
        $this->client->delete($url);

        GithubRepositoryGroup::where('github_repositories_id', $repoId)->where('github_groups_id', $groupId)->delete();

        return redirect()->back();
    }
}
