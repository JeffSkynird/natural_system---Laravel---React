<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Client extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'names',
        'document',
        'cellphone',
        'landline',
        'address',
        'email',
        'ip',
        'terminal',
        'user_id'
    ];
}
