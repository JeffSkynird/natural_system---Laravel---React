<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'product_id',
        'quantity',
        'supplier_id',
        'total',
        'status',
        'user_id',
        'ip',
        'terminal',
        'user_id'
    ];
}
