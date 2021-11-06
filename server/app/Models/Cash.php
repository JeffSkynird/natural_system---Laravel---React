<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cash extends Model
{
    use HasFactory;
    protected $fillable = [
        'start_amount',
        'final_amount',
        'status',
        'user_id',
        'ip',
        'terminal',
        'user_id',
        'closed_at'
    ];
}
