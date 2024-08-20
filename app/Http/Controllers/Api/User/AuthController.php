<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    //---Register user-----

    public function userRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required', 
            'username' => 'required|unique:users',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required_with:password|same:password'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'code' => 422,
                'errors' => $validator->errors(),
                'data' => [],
            ], 422);
        }
    
        try {
            $password = Hash::make($request->password);
    
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $password,
                'username' => $request->username,
                'type' => "2"
            ]);
    
            $role = Role::findByName('User', 'Api'); 
            $user->assignRole($role);
    
            return response()->json([
                'message' => 'Registration successful!',
                'code' => 201,
                'data' => $user,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while registering the user.',
                'code' => 500,
                'errors' => ['error' => 'Internal Server Error'],
                'data' => [],
            ], 500);
        }
    }
    //---end --

   //---userLogin---
    public function userLoginSubmit(Request $request){
        
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()->first()], 401);
    }

    $credentials = [ 
        "email" => $request->email,
        "password" => $request->password
    ];

    try {
        // Attempt to authenticate the user
        if (Auth::attempt($credentials)) {

            // Authentication successful, get the authenticated user
            $user = Auth::user();

            // Check User role
            if ($user->hasRole('User')) {

                // Generate token for the user
                $token = $user->createToken('authToken')->accessToken;

                $data = [
                    'token' => $token,
                    'email' => $user->email,
                    'type' => $user->type,
                    'name' => $user->name
                ];

                return response()->json(['message' => 'success', 'status' => 200, 'data' => $data]);
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    } catch (\Exception $e) {
        // Handle any exceptions that may occur
        return response()->json([
            'message' => 'An error occurred during login.',
            'code' => 500,
            'errors' => ['error' => $e->getMessage()],
            'data' => [],
        ], 500);
    }
}
//---end---

  // Logout function
  public function destroy(Request $request)
  {
      try {
        if (!$request->user()) {
            return response()->json(['error' => 'Unauthorized', 'code' => 401], 401);
        }

         $request->user()->token()->revoke();

        return response()->json(['message' => 'Logged out successfully.', 'code' => 200], 200);
      } catch (\Exception $e) {
        return response()->json(['error' => 'Internal Server Error!', 'code' => 500], 500);
     }
  }
    
}
