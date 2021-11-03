<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'jp_code',
        'supplier_code',
        'bar_code',
        'serie',
        'name',
        'image',
        'description',
        'stock',
        'list_price',
        'sale_price',
        'min_stock',
        'max_stock',
        'has_iva',
        'fraction',
        'unity_id',
        'status',
        'category_id',
        'ip',
        'terminal',
        'user_id'
    ];
}
