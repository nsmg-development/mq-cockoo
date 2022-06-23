<?php

namespace App\Services\Api\V1;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\ServerRequestInterface;

class AuthService
{
    public function pruneOldTokens(ServerRequestInterface $request)
    {
        $oauthClient = DB::table('oauth_clients')
            ->select(['id', 'secret'])->where('id', $request->getParsedBody()['client_id'])->first();

        DB::table('oauth_access_tokens')->where('client_id', $oauthClient->id)->delete();
    }
}
