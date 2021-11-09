<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Invoice extends Model
{
    use HasFactory,SoftDeletes; 
    protected $fillable = [
        'client_id',
        'total',
        'iva',
        'status',
        'ip',
        'discount',
        'final_consumer',
        'terminal',
        'user_id'
    ];
}
