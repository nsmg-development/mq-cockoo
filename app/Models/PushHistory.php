<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'push_id',
        'token',
        'status',
    ];
}
