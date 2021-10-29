<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kardex extends Model
{
    use HasFactory,SoftDeletes; 
    protected $fillable = [
        'product_id',
        'concept',
        'quantity',
        'stock',
        'type',
        'ip',
        'terminal',
        'user_id'
    ];
}
