<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Role;
use function GuzzleHttp\json_encode;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Role::query();

        if ($request->term != null) {
            $query = $query->whereIn('id', $request->term);
        }

        $roles = $query->orderBy('id', 'DESC')->paginate(25)->appends(request()->except(['page']));
        $permission = Permission::get();
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('roles.partials.list-roles', compact('roles'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $roles->render(),
                'count' => $roles->total(),
            ], 200);
        }

        return view('roles.index', compact('roles', 'permission'))->with('i', ($request->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission = Permission::get();

        return view('roles.create', compact('permission'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);
        $role = new Role();
        $role->name = $request->name;
        $role->save();
        $role_id = $role->id;

        $role->permissions()->sync($request->input('permission'));

        return redirect()->route('roles.index')
                         ->with('success', 'Role created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = $role->permissions;
        $data = [
            'role' => $role,
            'rolePermissions' => $rolePermissions,
        ];

        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = $role->permissions;

        $data = [
            'role' => $role,
            'rolePermissions' => $rolePermissions,
            'permission' => $permission,
        ];

        return $data;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'role_name' => 'required',
            'permission1' => 'required',
        ]);

        $role = Role::find($id);
        $role->name = $request->input('role_name');
        $role->save();

        $role->permissions()->sync($request->input('permission1'));
        $data = ['success' => 'Role updated successfully'];

        return $data;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Role::delete($id);

        return redirect()->route('roles.index')
                         ->with('success', 'Role deleted successfully');
    }

    public function unAuthorized()
    {
        return view('errors.401');
    }

    public function search_role(Request $request)
    {
        $permission = Permission::where('name', 'LIKE', '%'.$request->search_role.'%')->get();
        $permission_array = explode(',', $request->permission);

        $html = '<strong>Permission:</strong><br/>';
        foreach ($permission as $k => $value) {
            $checked = '';
            if (in_array($value['id'], $permission_array)) {
                $checked = 'checked';
            }

            $html .= '<label><input class="name mt-3 h-auto" name="permission[]" type="checkbox" value="'.$value['id'].'" '.$checked.'><span style="padding-left: 4px;">'.$value->name.'</span></label><br/>';
        }

        return json_encode($html);
    }
}
