<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ListingType;
use Illuminate\Support\Facades\Validator;

class ListingTypeController extends Controller
{
    //
      //Display the categories list page...
      public function index(){
        try{
           $listing_type = ListingType::get();
           return response()->json(['message'=> 'Listing Type Retrive Successfully!', 'code'=> 200, 'listing_type' => $listing_type], 200);
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
              'name' => 'required|unique:listing_types'
          ]);
  
          if ($validator->fails()) {
              return response()->json(['message' => 'Unprocessable Content' ,'code' => 422, 'errors' => $validator->errors()], 422);
          }
              $listing_type =ListingType::create([
                  'name'=> $request->name
              ]);

              return response()->json(['message' => 'Listing Type added successfully', 'code' => 201 , 'Category' => $listing_type], 201);
          } catch (\Exception $e) {
              return response()->json(['error' => 'Internal Server Error!' , 'code' => 500, 'data' => []], 500);
          }
      }

        //status active or inactive...
   public function status($id){
    try{
       $listing_type = ListingType::find($id);
          if($listing_type){
             if($listing_type->status){
                $listing_type->status= 0 ;
              }else{
                  $listing_type->status= 1 ;
                }
          $listing_type->save();
      }
      return response()->json(['message' => 'Status change successfully', 'code' => 201, 'status'=> $listing_type->status], 201);
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
      ListingType::find($id)->delete();
        return response()->json(['message' => 'Listing Type deleted successfully', 'code'=> 200 , 'data' => []], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Internal Server Error!' , 'code' => 500, 'data' => []], 500);
    }
}
  
}
