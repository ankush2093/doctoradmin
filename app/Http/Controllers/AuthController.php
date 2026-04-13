<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;

class AuthController extends Controller
{
    // Login admin and return token


    public function login(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'userName' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Eager load the role
        $user = AdminUser::with('role')->where('userName', $request->userName)->first();

        if (!$user || !Hash::check($request->password, $user->password) || !$user->isActive) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials or inactive user'], 401);
        }

        // Generate Sanctum token
        $token = $user->createToken(md5('anbv8734rvjew'))->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'userName' => $user->userName,
                'isActive' => $user->isActive,
                'role' => [
                    'id' => $user->role->id ?? null,
                    'roleType' => $user->role->roleType ?? null,
                ],
            ],
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Login failed.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

    // Logout current token
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json(['success' => true, 'message' => 'Logged out successfully.']);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
