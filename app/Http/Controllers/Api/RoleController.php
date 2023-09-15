<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;

class RoleController extends Controller
{
    public function index() { 
        return response()->json([
            'code' => 200,
            'message' => 'All roles successfully retrieved',
            'data' => RoleResource::collection(Role::all())
        ], 200);
    }

    public function create(Request $request) {

        $data = $request->validate([
            'name' => 'required'
        ]);

        if($data){
            $role = Role::create([
                'name' => $request->name
            ]);

            $roleCreated = $role->permissions()->sync($request->permissionIds);

            return response()->json([
                'code' => 200,
                'message' => 'Role ' . $request->name . ' successfully created',
                'data' => $roleCreated
            ], 200);

        } else {            
            return response()->json([
            'code' => 422,
            'message' => 'Field name required'
            ], 422);
        }

        return response()->json([
            'code' => 401,
            'message' => 'Unknown error'
        ]);        
    }

    public function edit(Request $request, $id) {

        $role = Role::find($id);

        $role->update([
            'name' => $request->name
        ]);

        $roleUpdated = $role->permissions()->sync($request->permissionIds);

        return response()->json([
            'code' => 200,
            'message' => 'Role ' . $request->name . ' successfully edit',
            'data' => $roleUpdated
        ], 200);
    }

    public function destroy($id) {
        $role = Role::find($id);

        $role->delete();

        return response()->json([
            'code' => 200,
            'message' => 'Role ' . $role->name . ' successfully deleted'
        ], 200);
    }
}
