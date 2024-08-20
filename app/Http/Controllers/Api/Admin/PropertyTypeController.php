<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PropertyType;
use Illuminate\Support\Facades\Validator;

class PropertyTypeController extends Controller
{
      //Display the categories list page...
      public function index(){
        try{
           $property_type = PropertyType::get();
           return response()->json(['message'=> 'Property Type Retrive Successfully!', 'code'=> 200, 'property_type' => $property_type], 200);
        }catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Something went wrong !']);
        }
     }
    //...end

      // Add function
      public function store(Request $request)
      {
          try {
          $validator = Validator::make($request->all(), [
              'name' => 'required|unique:property_types'
          ]);
  
          if ($validator->fails()) {
              return response()->json(['message' => 'Unprocessable Content' ,'code' => 422, 'errors' => $validator->errors()], 422);
          }
              $property_type =PropertyType::create([
                  'name'=> $request->name
              ]);

              return response()->json(['message' => 'Property Type added successfully', 'code' => 201 , 'Category' => $property_type], 201);
          } catch (\Exception $e) {
              return response()->json(['error' => 'Internal Server Error!' , 'code' => 500, 'data' => []], 500);
          }
      }

  //status active or inactive...
   public function status($id){
    try{
       $property_type = PropertyType::find($id);
          if($property_type){
             if($property_type->status){
                $property_type->status= 0 ;
              }else{
                  $property_type->status= 1 ;
                }
          $property_type->save();
      }
      return response()->json(['message' => 'Status change successfully', 'code' => 201, 'status'=> $property_type->status], 201);
    } 
   catch (\Exception $e) {
    return response()->json(['error' => 'Internal Server Error!' , 'code' => 500, 'data' => []], 500);
}
}
//...end 

// Delete function
public function delete($id)
{
    try {
      //  $data = Crypt::decrypt($id);
      PropertyType::find($id)->delete();
        return response()->json(['message' => 'Property Type deleted successfully', 'code'=> 200 , 'data' => []], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Internal Server Error!' , 'code' => 500, 'data' => []], 500);
    }
}
}
