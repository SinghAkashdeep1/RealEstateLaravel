<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;
use App\Models\Property;
use App\Models\PropertySubImages;
use App\Models\User;
use App\Models\ListingType;
use App\Models\PropertyType;
use App\Models\PropertySubType;



class PropertyController extends Controller
{
    //Display the properties types list page...
    public function index()
    {
        try {
            $listing_type = ListingType::get();
            $property_type = PropertyType::get();
            $property_sub_type = PropertySubType::get();

            return response()->json(['message' => 'Data Retrive Successfully!', 'code' => 200, 'listing_type' => $listing_type, 'property_type' => $property_type, 'property_sub_type' => $property_sub_type], 200);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Something went wrong !']);
        }
    }
    //...end

    //properties get
    public function propertyList()
    {
        try {
            $property_list = Property::get();
            $listing_type = ListingType::where('status',1)->get();
            $property_type = PropertyType::where('status',1)->get();
            $property_sub_type = PropertySubType::where('status',1)->get();
            $address = Address::get();
            return response()->json(['message' => 'Properties Retrive Successfully!', 'code' => 200, 'property_list' => $property_list, 'listing_type' => $listing_type, 'property_type' => $property_type, 'property_sub_type' => $property_sub_type, 'address' => $address], 200);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Something went wrong !']);
        }
    }
    //...end

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                // Validation rules for Property model
                'description' => 'required|string',
                'price' => 'required|string|min:0',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'bedrooms' => 'required|string|min:0',
                'bathrooms' => 'required|integer|min:0',
                'area' => 'required|string|min:0',
                'property_type' => 'required|string',
                'listing_type' => 'required|string',
                'property_sub_type' => 'required|string',
                 
                // Validation rules for Address model
                'location' => 'required|string',
                'city' => 'required|string',
                'pincode' => 'required|string|max:10',
                'state' => 'required|string',
                'country' => 'required|string',

                // Validation for sub_images
                'sub_images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation Error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Handle main image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . $image->getClientOriginalName();
                $destinationPath = 'backend/images';
                $image->move(public_path($destinationPath), $filename);
                $fullImagePath = $destinationPath . '/' . $filename;
            }

            // Create the Property
            $property = Property::create([
                'description' => $request->description,
                'price' => $request->price,
                'image' => isset($fullImagePath) ? $fullImagePath : null,
                'bedrooms' => $request->bedrooms,
                'bathrooms' => $request->bathrooms,
                'area' => $request->area,
                'property_type' => $request->property_type,
                'property_sub_type' => $request->property_sub_type,
                'listing_type' => $request->listing_type,
                'user_id' => Auth::id(), // Use the authenticated user's ID
                'created_by' => Auth::id(), // Use the authenticated user's ID
               // 'created_by' => '1',
                'status' => '1',
            ]);

            // Create Address
            $address = Address::create([
                'property_id' => $property->id,
                'location' => $request->location,
                'city' => $request->city,
                'pincode' => $request->pincode,
                'state' => $request->state,
                'country' => $request->country,
                'created_by' => Auth::id(), // Use the authenticated user's ID
                //'created_by' => '1',
                'status' => '1',
                'type' => '1',
            ]);

            // Create Property Sub Images
            $propertySubImages = [];

            if ($request->hasFile('sub_images')) {
                foreach ($request->file('sub_images') as $subImage) {
                    $filename = time() . '_' . $subImage->getClientOriginalName();
                    $destinationPath = 'backend/images';
                    $subImage->move(public_path($destinationPath), $filename);
                    $fullPath = $destinationPath . '/' . $filename;

                    PropertySubImages::create([
                        'property_id' => $property->id,
                        'sub_images' => $fullPath,
                        'created_by' => Auth::id(), // Use the authenticated user's ID
                        //   'created_by' => '1',

                        'status' => '1',
                    ]);
                }
            }

            // Update the authenticated user's type
            $user = Auth::user();
            if ($user) {
                $user->type = '3'; // Update the user_type to 3
                $user->save();
            }

            return response()->json([
                'message' => 'Property created successfully',
                'property' => $property,
                'address' => $address,
                'property_sub_images' => $propertySubImages,
                'user_type' => $user->type, // Return the updated user_type
                'code' => 201
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create property',
                'error' => $e->getMessage(),
                'code' => 500
            ], 500);
        }
    }


    public function show($id)
    {
        $property = Property::find($id);
        $sub_images = PropertySubImages::where('property_id', $id)->get();
        $address = Address::where('property_id', $id)->get();
        $user = User::find($property->user_id);
        return response()->json(['message' => 'property  page!', 'code' => 200, 'property' => $property, 'sub_images' => $sub_images, 'address' => $address, 'user' => $user, 'code' => 201], 200);
    }
}
