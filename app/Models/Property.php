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
       'area',
       'description',
       'property_type',
       'type',
       'created_by',
       'status'
    ];

    protected $hidden = [
     
    ];

   
}
