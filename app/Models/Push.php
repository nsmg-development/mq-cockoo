<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Push 본문, 발송자(발송 주체 서비스), 본체 내역
*/
class Push extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'title',
        'body',
    ];
}
