<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PropertyType;
use App\Models\PropertySubType;
use Illuminate\Support\Facades\Validator;

class PropertySubTypeController extends Controller
{
    //
     //Display the property sub list page...
     public function index(){
        try{
           $property_sub_type = PropertySubType::get();
           return response()->json(['message'=> 'Property Sub Type Retrive Successfully!', 'code'=> 200, 'property_sub_type' => $property_sub_type], 200);
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
              'name' => 'required|unique:property_sub_types',
              'property_type_id' => 'required|exists:property_types,id'
          ]);
  
          if ($validator->fails()) {
              return response()->json(['message' => 'Unprocessable Content' ,'code' => 422, 'errors' => $validator->errors()], 422);
          }
              $property_sub_type =PropertySubType::create([
                  'name'=> $request->name,
                  'property_type_id' => $request->property_type_id,

              ]);

              return response()->json(['message' => 'Property Sub Type added successfully', 'code' => 201 , 'property_sub_type' => $property_sub_type], 201);
          } catch (\Exception $e) {
              return response()->json(['error' => 'Internal Server Error!' , 'code' => 500, 'data' => []], 500);
          }
      }

  //status active or inactive...
   public function status($id){
    try{
       $property_sub_type = PropertySubType::find($id);
          if($property_sub_type){
             if($property_sub_type->status){
                $property_sub_type->status= 0 ;
              }else{
                  $property_sub_type->status= 1 ;
                }
          $property_sub_type->save();
      }
      return response()->json(['message' => 'Status change successfully', 'code' => 201, 'status'=> $property_sub_type->status], 201);
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
      PropertySubType::find($id)->delete();
        return response()->json(['message' => 'Property subtype deleted successfully', 'code'=> 200 , 'data' => []], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Internal Server Error!' , 'code' => 500, 'data' => []], 500);
    }
}
}
