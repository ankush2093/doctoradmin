<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Exception;

class AdminUserController extends Controller
{
    // Create new admin user
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'userName'  => 'required|string|max:30|unique:admin_users',
                'password'  => 'required|string|min:6',
                'adminRole' => 'nullable|exists:roles,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['success'=>false, 'message' => 'Validation error.', 'errors'=>$validator->errors()], 422);
            }

            $resolvedRoleId = $request->adminRole;

            if (!$resolvedRoleId) {
                $existingAdminRole = Role::where('roleType', 'admin')->first();

                if ($existingAdminRole) {
                    $resolvedRoleId = $existingAdminRole->id;
                } else {
                    $firstRole = Role::first();

                    if ($firstRole) {
                        $resolvedRoleId = $firstRole->id;
                    } else {
                        $newRole = Role::create([
                            'roleType' => 'admin',
                        ]);
                        $resolvedRoleId = $newRole->id;
                    }
                }
            }

            $user = AdminUser::create([
                'userName'  => $request->userName,
                'password'  => Hash::make($request->password),
                'adminRole' => $resolvedRoleId,
                'isActive'  => true,
            ]);

            return response()->json(['success'=>true, 'message'=>'User created successfully.', 'user'=>$user], 201);
        } catch (Exception $e) {
            return response()->json(['success'=>false, 'message'=>$e->getMessage()], 500);
        }
    }

    // Update admin user by id
    public function update(Request $request, $id)
    {
        try {
            $user = AdminUser::where('id', $id)->where('isActive', true)->first();
            if (!$user) {
                return response()->json(['success'=>false, 'message'=>'User not found or inactive'], 404);
            }

            $validator = Validator::make($request->all(), [
                'userName'  => 'sometimes|required|string|max:30|unique:admin_users,userName,'.$user->id,
                'password'  => 'sometimes|required|string|min:6',
                'adminRole' => 'sometimes|required|exists:roles,id',
                'isActive'  => 'sometimes|required|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['success'=>false,  'message' => 'Validation error.', 'errors'=>$validator->errors()], 422);
            }

            if ($request->has('password')) {
                $user->password = Hash::make($request->password);
            }
            if ($request->has('userName')) {
                $user->userName = $request->userName;
            }
            if ($request->has('adminRole')) {
                $user->adminRole = $request->adminRole;
            }
            if ($request->has('isActive')) {
                $user->isActive = $request->isActive;
            }

            $user->save();

            return response()->json(['success'=>true, 'message'=>'User updated successfully.', 'user'=>$user]);
        } catch (Exception $e) {
            return response()->json(['success'=>false, 'message'=>$e->getMessage()], 500);
        }
    }

    // List all admin users
    public function getAll()
    {
        try {
            $users = AdminUser::where('isActive', true)->get();
            return response()->json(['success' => true, 'message'=>'User retrieved successfully.', 'users' => $users]);
        } catch (Exception $e) {
            return response()->json(['success'=>false, 'message'=>$e->getMessage()], 500);
        }
    }

    // Show admin user by id
    public function getById($id)
    {
        try {
            $user = AdminUser::where('id', $id)->where('isActive', true)->first();
            if (!$user) {
                return response()->json(['success'=>false, 'message'=>'User not found or inactive'], 404);
            }
            return response()->json(['success'=>true, 'message'=>'User retrieved successfully.', 'user'=>$user]);
        } catch (Exception $e) {
            return response()->json(['success'=>false, 'message'=>$e->getMessage()], 500);
        }
    }

    // Delete admin user by id
    public function delete($id)
    {
        try {
            $user = AdminUser::find($id);
            if (!$user) {
                return response()->json(['success'=>false, 'message'=>'User not found'], 404);
            }

            $user->update(['isActive' => false]);

            return response()->json(['success'=>true, 'message'=>'User deleted successfully.']);
        } catch (Exception $e) {
            return response()->json(['success'=>false, 'message'=>$e->getMessage()], 500);
        }
    }
}
