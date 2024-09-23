<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCart extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function productDetails()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
