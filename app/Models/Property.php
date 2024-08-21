<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
       'price',
       'bedrooms',
       'bathrooms',
       'area',
       'description',
       'property_type',
       'property_sub_type',
       'listing_type',
       'type',
       'image',
       'created_by',
       'status',
       'user_id'
    ];

    protected $hidden = [
     
    ];
}
