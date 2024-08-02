<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertySubImages extends Model
{
    use HasFactory;

    protected $fillable = [
    'property_id',
    'sub_images',
    'type',
    'created_by',
    'status',
    ];
}
