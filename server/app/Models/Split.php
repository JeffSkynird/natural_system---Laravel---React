<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Split extends Model
{
    use HasFactory;
    protected $fillable = [
        'cash_id',
        'denomination_id',
        'quantity'
    ];
}
