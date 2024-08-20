<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertySubType extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'property_type_id',
        'type',
        'created_by',
        'status'
    ];
}
