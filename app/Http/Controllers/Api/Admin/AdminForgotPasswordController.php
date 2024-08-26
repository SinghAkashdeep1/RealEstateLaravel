<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Hash;

class AdminForgotPasswordController extends Controller
{
     // Submit forgot password form
     public function submitAdminForgetPasswordForm(Request $request)
     {
         $validator = Validator::make($request->all(), [
             'email' => 'required|email|exists:users',
         ]);
 
         if ($validator->fails()) {
             return response()->json(['message' => 'Unprocessable Content' ,'code' => 422, 'errors' => $validator->errors()], 422);
         }
 
         try {
             $token = Str::random(64);
             DB::table('password_resets')->insert([
                 'email' => $request->email,
                 'token' => $token,
                 'created_at' => Carbon::now()
             ]);
 
             return response()->json(['message' => 'token generated successfully', 'token' => $token], 200);
 
         } catch (\Exception $e) {
             return response()->json(['error' => 'Something went wrong.', 'code' => 500, 'data' =>[]], 500);
         }
     }

    // Show reset password form
    public function showAdminResetPasswordForm($token)
    {
        return response()->json(['message' => 'token link show', 'code' => 200, 'token' => $token], 200);
    }

    // Submit reset password form
    public function submitAdminResetPasswordForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Unprocessable Content' ,'code' => 422, 'errors' => $validator->errors()], 422);
        }

        try {
            $updatePassword = DB::table('password_resets')
                ->where([
                    'email' => $request->email,
                    'token' => $request->token
                ])
                ->first();

            if (!$updatePassword) {
                return response()->json(['error' => 'Invalid token.' , 'code' => 400 , 'data' =>[]], 400);
            }

            $user = User::where('email', $request->email)
                ->update(['password' => Hash::make($request->password)]);

            DB::table('password_resets')->where(['email' => $request->email])->delete();

            return response()->json(['message' => 'Your password has been changed.', 'code' => 200 ] , 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while resetting password.' , 'code'=> 500, 'data' => []], 500);
        }
    }
}
