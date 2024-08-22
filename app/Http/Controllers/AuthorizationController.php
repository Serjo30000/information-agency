<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\FullName;
use App\Rules\PhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthorizationController extends Controller
{
    public function register(Request $request)
    {
        $rules = [
            'login' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'fio' => ['required','string','max:255',new FullName()],
            'phone' => ['required', 'string', 'max:15', 'unique:users', new PhoneNumber],
            'roles' => 'required|array',
            'roles.*' => 'string|exists:roles,name',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $user = User::create([
            'login' => $request->input('login'),
            'password' => Hash::make($request->input('password')),
            'fio' => $request->input('fio'),
            'phone' => $request->input('phone'),
        ]);

        $user->assignRole($request->input('roles'));

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'message' => 'Registration successful'
        ], 201);
    }

    public function login(Request $request)
    {
        $rules = [
            'login' => 'required|string',
            'password' => 'required|string',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $credentials = $request->only('login', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'access_token' => $token,
                'token_type' => 'Bearer',
                'message' => 'Login successful'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 401);
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        if ($user != null && $user->tokens()->count()>0){
            $user->tokens()->delete();
        }


        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ], 200);
    }

    public function checkAuth(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        if ($user != null) {
            return response()->json([
                'success' => true,
                'message' => 'User is authenticated',
                'user' => Auth::guard('sanctum')->user()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'User is not authenticated'
        ], 401);
    }
}
