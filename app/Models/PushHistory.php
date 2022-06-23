<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 개별 수신자 수신 내역.
 * 성능 문제로 개별 일일이 저장 X
 * 건당 발송한 대량 발송 성공 여부 JSON 화 저장
*/
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
