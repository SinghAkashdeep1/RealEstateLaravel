<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Address;
use App\Models\PropertyType;
use App\Models\ListingType;
use App\Models\PropertySubType;
use App\Models\PropertySubImages;
use App\Models\User;

use Illuminate\Support\Facades\Validator;

class PropertyAdminController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = 5;
            $page = $request->query('page', 1);
            $property = Property::paginate($perPage, ['*'], 'page', $page);
            $address = Address::all();

            return response()->json([
                'message' => 'Data Retrieve Successfully!',
                'code' => 200,
                'property' => $property,
                'address' => $address
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error!', 'code' => 500], 500);
        }
    }

    public function show($id)
    {
        $property = Property::find($id);
        $sub_images = PropertySubImages::where('property_id', $id)->get();
        $address = Address::where('property_id', $id)->get();
        $user = User::find($property->user_id);
        return response()->json(['message' => 'Data Retrieve Successfully!', 'code' => 200, 'property' => $property, 'sub_images' => $sub_images,
        'address' =>$address, 'user' =>$user], 200);
    }
    //end

     //status active or inactive...
     public function status($id){
        try {
            $property = Property::findOrFail($id);
         if($property){
          if($property->status){
               $property->status= 0 ;
          }else{
               $property->status= 1 ;
       }
           $property->save();
       }
       return response()->json(['message' => 'Status change successfully', 'code' => 201 ,'status'=> $property->status ], 201);
     }
      catch (\Exception $e) {
        return response()->json(['error' => 'Internal Server Error!' , 'code' => 500], 500);
      }
    }
   //...end 
}
