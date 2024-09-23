<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'price', 'quantity', 'is_bundle', 'package_quantity',
        'bundle_discount', 'is_subscribable', 'schedule_type', 'schedule'
    ];

    protected $casts = [
        'bundle_discount' => 'array',
        'schedule' => 'array',
    ];
}
