<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $fillable = [
        'property_id',
        'location',
        'pincode',
        'city',
        'state',
        'country',
        'created_by',
        'status',
        'type'
     ];
}
