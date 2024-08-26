<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Property;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;

class UserDashboardController extends Controller
{
    public function userPropList(Request $request)
    {
        try {
            // Get the authenticated user
            $user = Auth::user();
    
            if (!$user) {
                return response()->json(['error' => 'Unauthorized', 'code' => 401], 401);
            }
    
            // Fetch all properties for the authenticated user
            $properties = Property::where('user_id', $user->id)->get();
    
            // Get all property IDs
            $propertyIds = $properties->pluck('id');
    
            // Fetch addresses for these properties
            $addresses = Address::whereIn('property_id', $propertyIds)->get()->keyBy('property_id');
    
            // Combine properties with their addresses
            $propertiesWithAddresses = $properties->map(function ($property) use ($addresses) {
                return [
                    'id' => $property->id,
                    'image' => $property->image,
                    'price' => $property->price,
                    'status' => $property->status,
                    'description' => $property->description,
                    'address' => isset($addresses[$property->id]) ? [
                        'location' => $addresses[$property->id]->location,
                        'city' => $addresses[$property->id]->city,
                        'pincode' => $addresses[$property->id]->pincode,
                        'state' => $addresses[$property->id]->state,
                        'country' => $addresses[$property->id]->country,
                    ] : null,
                ];
            });
    
            return response()->json([
                'message' => 'User profile page',
                'code' => 200,
                'user_id' => $user->id,
                'properties' => $propertiesWithAddresses,
            ], 200);
    
        } catch (\Exception $e) {
            // Log the exception message for debugging purposes
    
            return response()->json([
                'error' => 'Internal Server Error!',
                'code' => 500,
                'data' => []
            ], 500);
        }
    }
    
    
}
