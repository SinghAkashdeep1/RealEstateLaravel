<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends Controller
{
    //
      //---adminLogin---
      public function adminLoginSubmit(Request $request){
        
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
                if ($user->hasRole('Admin')) {
    
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
