<?php

namespace App\Services\Api\V1;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AuthService
{
    public function token(int $clientId)
    {
        $secret = DB::table('oauth_clients')->select(['id', 'secret'])->where('id', $clientId)->first();

        DB::table('oauth_access_tokens')->where('client_id', $secret->id)->delete();

        $response = Http::asForm()->post(env('APP_URL').'/oauth/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $secret->id,
            'client_secret' => $secret->secret,
            'scope' => '',
        ]);

        return collect($response->json());
    }
}
