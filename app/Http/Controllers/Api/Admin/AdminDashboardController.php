<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Property;
use App\Models\ListingType;
use App\Models\PropertyType;
use DateTime;

class AdminDashboardController extends Controller
{
    // Display admin's dashboard...
    public function index(Request $request)
    {
        try {
            // Dashboard counts
            $totalUsers = User::where('type', '2')->orWhere('type', '3')->count();
            $totalAllProperties = Property::where('status', '1')->count();
            $totalAllListingTypes = ListingType::where('status', '1')->count();
            $totalAllPropertyTypes = PropertyType::where('status', '1')->count();

            // Get recent users
            $recentUsers = User::where('type', '2')
                ->orWhere('type', '3')
                ->where('created_at', '>', new DateTime('last day of previous month'))
                ->paginate(5); 

            // Get the latest 5 recent properties without pagination
            $recentProperties = Property::where('created_at', '>', new DateTime('last day of previous month'))
                ->orderBy('created_at', 'desc') 
                ->take(5) 
                ->get();

            $unsoldProperties = Property::where('status', '1')->count();
            $soldProperties = Property::where('status', '0')->count();

            return response()->json([
                'message' => 'Data retrieved successfully',
                'code' => 200,
                'Users' => $totalUsers,
                'properties' => $totalAllProperties,
                'listingType' => $totalAllListingTypes,
                'propertyType' => $totalAllPropertyTypes,
                'Recent_users' => $recentUsers,
                'recent_properties' => $recentProperties,
                'unsold_properties' => $unsoldProperties,
                'sold_properties' => $soldProperties
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error!', 'code' => 500], 500);
        }
    }
    // ...end admin's dashboard
}
