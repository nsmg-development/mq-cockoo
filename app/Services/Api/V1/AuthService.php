<?php

namespace App\Services\Api\V1;

use Illuminate\Support\Facades\DB;
use Psr\Http\Message\ServerRequestInterface;

class AuthService
{
    /**
     * 새 토큰 발급전 기존 토큰 삭제
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function pruneOldTokens(ServerRequestInterface $request)
    {
        $oauthClient = DB::table('oauth_clients')
            ->select(['id', 'secret'])->where('id', $request->getParsedBody()['client_id'])->first();

        DB::table('oauth_access_tokens')->where('client_id', $oauthClient->id)->delete();
    }
}
