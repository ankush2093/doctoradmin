<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class RoleController extends Controller
{
    // Create new role
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'roleType' => 'required|string|max:100|unique:roles',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $role = Role::create([
                'roleType' => $request->roleType,
                'isActive' => true,
            ]);

            return response()->json(['success' => true, 'message' => 'Role created successfully.', 'role' => $role], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Update role by id
    public function update(Request $request, $id)
    {
        try {
            $role = Role::where('id', $id)->where('isActive', true)->first();
            if (!$role) {
                return response()->json(['success' => false, 'message' => 'Role not found or inactive'], 404);
            }

            $validator = Validator::make($request->all(), [
                'roleType' => 'required|string|max:100|unique:roles,roleType,' . $role->id,
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $role->roleType = $request->roleType;
            $role->save();

            return response()->json(['success' => true, 'message' => 'Role updated successfully.', 'role' => $role]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // List all roles
    public function getAll()
    {
        try {
            $roles = Role::where('isActive', true)->get();
            return response()->json(['success' => true, 'message' => 'Role retrieved successfully.', 'roles' => $roles]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Show role by id
    public function getById($id)
    {
        try {
            $role = Role::where('id', $id)->where('isActive', true)->first();
            if (!$role) {
                return response()->json(['success' => false, 'message' => 'Role not found or inactive'], 404);
            }
            return response()->json(['success' => true, 'message' => 'Role retrieved successfully.', 'role' => $role]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Delete role by id
    public function delete($id)
    {
        try {
            $role = Role::find($id);
            if (!$role) {
                return response()->json(['success' => false, 'message' => 'Role not found'], 404);
            }

            $role->update(['isActive' => false]);

            return response()->json(['success' => true, 'message' => 'Role delete successfully.', 'message' => 'Role deleted.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
