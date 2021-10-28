<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceProduct extends Model
{
    use HasFactory,SoftDeletes; 
    protected $fillable = [
        'invoice_id',
        'product_id',
        'quantity',
        'subtotal',
        'ip',
        'final_consumer',
        'terminal',
        'user_id'
    ];
}
