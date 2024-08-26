<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserManagentController extends Controller
{
    //
    public function index()
    {
        try {
            // Set the number of items per page
            $perPage = 10;
    
            // Use paginate to get paginated results
            $users = User::where('type', '2')->orWhere('type', '3')->paginate($perPage);
    
            // Return the paginated results in the response
            return response()->json([
                'message' => 'Data Retrieve Successfully!',
                'code' => 200,
                'users' => $users
            ], 200);
        } catch (\Exception $e) {
            // Handle any exceptions and return an error response
            return response()->json([
                'error' => 'Internal Server Error!',
                'code' => 500
            ], 500);
        }
    }
    

     // Display the user add page with accessing Roles
     public function create()
     {
         try {
             $roles = Role::get();
             return response()->json(['message'=> 'Data Retrieve Successfully!', 'code' => 200, 'roles' => $roles], 200);
         } catch (\Exception $e) {
             return response()->json(['error' => 'Internal Server Error!' , 'code' => 500], 500);
         }
     }

      // Add function
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Unprocessable Content' ,'code' => 422, 'errors' => $validator->errors()], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'type' => $request->type,
                'status' => '1'
            ]);

            if($request->type =='1'){
            $role = Role::findByName('Admin', 'Api'); 
            $user->assignRole($role);

            }
            if($request->type =='2'){
                $role = Role::findByName('User', 'Api'); 
                $user->assignRole($role);
    
                }
        
            return response()->json(['message' => 'User created successfully', 'data' => $user , 'code' => '201' ],201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error!' , 'code' => 500], 500);
        }
    }

      //status active or inactive...
      public function status($id){
        try {
            $user = User::findOrFail($id);
         if($user){
          if($user->status){
               $user->status= 0 ;
          }else{
               $user->status= 1 ;
       }
           $user->save();
       }
       return response()->json(['message' => 'Status change successfully', 'code' => 201 ,'status'=> $user->status ], 201);
     }
      catch (\Exception $e) {
        return response()->json(['error' => 'Internal Server Error!' , 'code' => 500], 500);
      }
    }
   //...end 

     //...show edit page...
     public function show($id){
        // $data = Crypt::decrypt($id);
        $user = User::find($id);
        return response()->json(['message' => 'Data Retrieve Successfully!', 'code' => 200 ,'user' => $user], 200);
    }
    //end

    // Update function
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|email',
            'type' => 'required|in:1,2' 
        ]);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Unprocessable Content', 'code' => 422, 'errors' => $validator->errors()], 422);
        }
    
        try {
            $user = User::findOrFail($id);
            
            // Update user information
            $user->update([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'type' => $request->type,
            ]);
    
            // Determine new role based on type
            $newRoleName = $request->type == '2' ? 'Admin' : 'User';
            $newRole = Role::findByName($newRoleName, 'Api');
    
            // Check if user has an existing role and remove it
            if ($user->roles->isNotEmpty()) {
                $user->removeRole($user->roles[0]);
            }
    
            // Assign the new role
            $user->assignRole($newRole);
    
            return response()->json(['message' => 'User updated successfully', 'code' => 200, 'data' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error!', 'code' => 500], 500);
        }
    }
    

    // Delete function
    public function delete($id)
    {
        try {
            User::find($id)-> delete();
            return response()->json(['message' => 'User deleted successfully' , 'code' => 200 , 'data'=>[]], 200);
        }
     catch (\Exception $e) {
        return response()->json(['error' => 'Internal Server Error!' , 'code' => 500], 500);
    }
    }
}
