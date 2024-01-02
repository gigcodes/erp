<?php

namespace App\Http\Controllers\Github;

use Illuminate\Http\Request;
use App\Github\GithubOrganization;
use App\Http\Controllers\Controller;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $githubOrganizations = GithubOrganization::get();

        return view('github.organizations.index', compact('githubOrganizations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $mode = 'add';

        return view('github.organizations.create', compact('mode'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'token' => 'required',
        ]);

        $mode = 'created';

        if (strlen($request->organization_id) > 0) {
            $mode = 'updated';

            $githubOrganization = GithubOrganization::find($request->organization_id);
        } else {
            $githubOrganization = new GithubOrganization;
        }

        $githubOrganization->name = $request->name;
        $githubOrganization->username = $request->username;
        $githubOrganization->token = $request->token;
        $githubOrganization->save();

        \Session::flash('sucess', 'Success! Organization has been ' . $mode . '.');

        return redirect('github/organizations');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $isDeleted = GithubOrganization::destroy($id);

        \Session::flash('sucess', 'Success! Organization has been deleted.');

        return redirect('github/organizations');
    }
}
