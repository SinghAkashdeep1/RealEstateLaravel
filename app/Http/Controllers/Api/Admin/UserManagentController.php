<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class UserManagentController extends Controller
{
    //
    public function index()
    {
        try {
            $users = User::where('type', '1')->get();
            return response()->json(['message' => 'Data Retrieve Successfully!', 'code' => 200 ,'users' => $users], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error!' , 'code' => 500], 500);
        }
    }
}
