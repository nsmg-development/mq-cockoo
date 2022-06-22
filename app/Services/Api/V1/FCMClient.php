<?php

namespace App\Services\Api\V1;

use Google_Client;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FCMClient
{
    private ?int $clientId = null;
    private string $clientName = '';
    private array $header = [];
    private array $notification = [];
    private string $fcmUrl = '';
    private Google_Client $client;

    public function __construct(Google_Client $client)
    {
        $this->client = $client;
    }

    public function client(int $clientId): FCMClient
    {
        if(!$this->clientId) $this->clientId = $clientId;
        if(!$this->clientName) $this->setClientName($clientId);
        if(!$this->header) $this->setHeader();
        if(!$this->fcmUrl) $this->setFCMUrl();

        return $this;
    }

    private function setClientName(int $clientId)
    {
        $credential_name = DB::table('oauth_clients')->where('id', $clientId)->select(['name'])->first()->name;
        $this->clientName = str_replace(' ClientCredentials Grant Client', '', $credential_name);
    }

    private function setFCMUrl ()
    {
        $raw = env('FCM_PUSH_RAW_URL');
        $this->fcmUrl = str_replace('{%media%}', $this->clientName, $raw);
    }

    private function setHeader ()
    {
        $this->header = [
            'Content-Type' => 'application/http',
            'Content-Transfer-Encoding' => 'binary',
            'Authorization' => "Bearer {$this->getAccessToken()}",
        ];
    }

    public function setNotification (string $title, string $body)
    {
        $this->notification = [
            "title" => $title,
            "body" => $body,
        ];
    }

    private function getAccessToken(): string
    {
        $this->client->setAuthConfig(base_path("authjson/{$this->clientName}.json"));
        $this->client->addScope(env('FCM_CREDENTIAL_SCOPE'));
        $this->client->fetchAccessTokenWithAssertion();
        return preg_replace('/\.\./', '', $this->client->getAccessToken()['access_token']);
    }

    public function send(string $token): Response
    {
        $body = [
            "message" => [
                "token" => $token,
                "notification" => $this->notification,
            ],
        ];

        return Http::withHeaders($this->header)->post($this->fcmUrl, $body);
    }

    public function getHeader(): array
    {
        return $this->header;
    }

    public function getFcmUrl(): string
    {
        return $this->fcmUrl;
    }

    public function getBody(string $token)
    {
        return [
            "message" => [
                "token" => $token,
                "notification" => $this->notification,
            ],
        ];
    }
}
