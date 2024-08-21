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
use Illuminate\Support\Facades\DB;



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

    //get property types for search
    public function propertyTypes()
    {
        try {
            $property_types = PropertyType::get();

            return response()->json(['message' => 'Property Types  Retrive Successfully!', 'code' => 200, 'property_types' => $property_types], 200);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Something went wrong !']);
        }
    }
    //...end
    
    //property list with list types and search filters
    public function propertyList(Request $request)
    {
        try {
            // Initialize the base query with raw SQL
            $query = DB::table('properties')
                ->leftJoin('addresses', 'properties.id', '=', 'addresses.property_id')
                ->select('properties.*');
    
            // Check if there is a search parameter
            if ($request->has('search')) {
                $search = $request->input('search');
    
                // Apply search filters using raw SQL
                $query->where(function ($q) use ($search) {
                    $q->where('properties.description', 'LIKE', "%{$search}%")
                        ->orWhere('properties.price', 'LIKE', "%{$search}%")
                        ->orWhere('addresses.location', 'LIKE', "%{$search}%")
                        ->orWhere('addresses.city', 'LIKE', "%{$search}%")
                        ->orWhere('addresses.pincode', 'LIKE', "%{$search}%");
                });
            }
    
            // Check if there is a type parameter
            if ($request->has('type') && $request->input('type') != '') {
                $type = $request->input('type');
                $query->where('properties.property_type', $type);
            }
    
            // Check number of bedrooms and bathrooms
            if ($request->has('bedrooms') && $request->input('bedrooms') != '') {
                $bedrooms = $request->input('bedrooms');
                $query->where('properties.bedrooms', $bedrooms);
            }
    
            if ($request->has('bathrooms') && $request->input('bathrooms') != '') {
                $bathrooms = $request->input('bathrooms');
                $query->where('properties.bathrooms', $bathrooms);
            }
    
            // Validate and apply min and max price filters
            $min_price = $request->has('min_price') && is_numeric($request->input('min_price')) ? (float) $request->input('min_price') : null;
            $max_price = $request->has('max_price') && is_numeric($request->input('max_price')) ? (float) $request->input('max_price') : null;
    
            if ($min_price !== null && $max_price !== null) {
                // Both min_price and max_price are set
                $query->whereBetween('properties.price', [$min_price, $max_price]);
            } else {
                if ($min_price !== null) {
                    // Only min_price is set
                    $query->where('properties.price', '>=', $min_price);
                }
                
                if ($max_price !== null) {
                    // Only max_price is set
                    $query->where('properties.price', '<=', $max_price);
                }
            }
    
            // Fetch the filtered properties
            $property_list = $query->get();
    
            // Fetch other data
            $listing_type = DB::table('listing_types')->where('status', 1)->get();
            $property_type = DB::table('property_types')->where('status', 1)->get();
            $property_sub_type = DB::table('property_sub_types')->where('status', 1)->get();
            $address = DB::table('addresses')->get();
    
            // Return response
            return response()->json([
                'message' => 'Properties Retrieved Successfully!',
                'code' => 200,
                'property_list' => $property_list,
                'listing_type' => $listing_type,
                'property_type' => $property_type,
                'property_sub_type' => $property_sub_type,
                'address' => $address
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong!',
                'code' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    

    //add
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
