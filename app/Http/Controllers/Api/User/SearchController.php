<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Property;

class SearchController extends Controller
{
    //
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'searchQuery' => 'required|string',
            'selectedOption' => 'nullable|string',
        ]);

        // Extract the data
        $search = $request['search'] ?? "";
        
        if($search != ""){
            $property_list = Property::get();
        }else{

        }
    }
}
