<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    //
    protected $table = 'servicios';
    protected $fillable = ['user_id', 'product_id', 'quantity', 'transaction_type'];
}
