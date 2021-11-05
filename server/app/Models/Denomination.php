<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Denomination extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'amount',
        'user_id',
        'ip',
        'terminal',
        'user_id'
    ];
}
