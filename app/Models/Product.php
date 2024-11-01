<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $table = 'productos';
    protected $fillable = [
        'product_name',
        'quantity',
        'price',
    ];
}
