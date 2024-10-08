<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Validator;

class AdminProfileController extends Controller
{
    // Display the admin profile
    public function adminProfile(Request $request)
    {
        try {
            $user = Auth::user();
            return response()->json([ 'message' => 'Admin profile page', 'code'=> 200, 'user' => $user], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Internal Serve Error!' , 'code' => 500 , 'data' => []], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            // Check if the user is authenticated
            if (!auth()->check()) {
                return response()->json(['error' => 'Unauthorized', 'code' => 401], 401);
            }
    
            // Get the currently authenticated user
            $user = auth()->user();
    
            // Define validation rules
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
            ];
    
            // Create a validator instance
            $validator = Validator::make($request->all(), $rules);
    
            // Check if validation fails
            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation Error',
                    'code' => 422,
                    'messages' => $validator->errors()
                ], 422);
            }
    
            // Update the user's profile
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
    
            return response()->json([
                'message' => 'Profile updated successfully!',
                'code' => 200,
                'Admin' => $user
            ], 200);
    
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error!',
                'code' => 500,
                'data' => []
            ], 500);
        }
    }

    // Change admin's password
    public function changePasswordSave(Request $request)
    {
    // Change password validations 
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|string|min:6|different:current_password|confirmed',
        'new_password_confirmation' => 'required|string|same:new_password',
    ]);

    try {
        // Check if the current password matches the authenticated user's password
        if (!(Hash::check($request->current_password, Auth::user()->password))) {
            return response()->json(['error' => 'Your current password does not match the password you provided. Please try again.', 'code' => 400, 'data' => []], 400);
        }

        // If the current password is correct, update the user's password
        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Return success response
        return response()->json(['message' => 'Password changed successfully!', 'data' => $user , 'code' => 200], 200);

    } catch (\Exception $e) {
        // Handle exceptions
        return response()->json(['error' => 'Something went wrong!', 'code' => 500 , 'data' => []], 500);
    }
}
}
