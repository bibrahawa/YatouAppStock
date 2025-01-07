<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;

class RolePermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
        $roles = Role::all();
        return view('acl.role.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getNewRole()
    {
        $role = new Role;
        return view('acl.role.form', compact('role'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postRole(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
        ]);

        $role = new Role;
        $role->name = $request->get('role_name');
        $role->save();

        $message = trans('core.changes_saved');
        return redirect()->back()->withSuccess($message);
    }

    /**
     * Display the specified resource.
     *
     * @param  Role  $role
     * @return \Illuminate\Http\Response
     */
    public function setRolePermissions(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissionNameLists = [];

        if ($role->permissions->count() != 0) {
            $rolePermissions = $role->permissions;
            foreach ($rolePermissions as $rolePermission) {
                $rolePermissionNameLists[] = ucwords($rolePermission->type) . ' ' . ucwords($rolePermission->name);
            }
        }
        return view('acl.role-permissions.form', compact('role', 'permissions', 'rolePermissionNameLists'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postRolePermissions(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($request->get('role_id'));

        $newPermissions = $request->get('permissions', []);
        $role->permissions()->sync($newPermissions);

        $message = trans('core.changes_saved');
        return redirect()->route('role.index')->withSuccess($message);
    }
}
