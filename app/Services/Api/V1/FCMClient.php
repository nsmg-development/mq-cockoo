<?php

namespace App\Services\Api\V1;

use Google\Exception;
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

    /**
     * @param Google_Client $client
     */
    public function __construct(Google_Client $client)
    {
        $this->client = $client;
    }

    /**
     * init FCM client prepare and return client
     *
     * @param int $clientId
     * @return $this
     * @throws Exception
     */
    public function client(int $clientId): FCMClient
    {
        if(!$this->clientId) $this->clientId = $clientId;
        if(!$this->clientName) $this->setClientName($clientId);
        if(!$this->header) $this->setHeader();
        if(!$this->fcmUrl) $this->setFCMUrl();

        return $this;
    }

    /**
     * 매체별 클라이언트 이름 지정
     * 이 이름으로 FCM AUTH json 을 찾고,
     * fcm endpoint 를 설정함. 통일성 중요.
     *
     * @param int $clientId
     * @return void
     */
    private function setClientName(int $clientId)
    {
        $credential_name = DB::table('oauth_clients')->where('id', $clientId)->select(['name'])->first()->name;
        $this->clientName = str_replace(' ClientCredentials Grant Client', '', $credential_name);
    }

    /**
     * FCM endpoint 설정
     *
     * @return void
     */
    private function setFCMUrl ()
    {
        $raw = env('FCM_PUSH_RAW_URL');
        $this->fcmUrl = str_replace('{%media%}', $this->clientName, $raw);
    }

    /**
     * FCM access token 받음.
     * header 설정
     *
     * @return void
     * @throws Exception
     */
    private function setHeader ()
    {
        $this->header = [
            'Content-Type' => 'application/http',
            'Content-Transfer-Encoding' => 'binary',
            'Authorization' => "Bearer {$this->getAccessToken()}",
        ];
    }

    /**
     * 메시지 본문 생성
     *
     * @param string $title
     * @param string $body
     * @return void
     */
    public function setNotification (string $title, string $body)
    {
        $this->notification = [
            "title" => $title,
            "body" => $body,
        ];
    }

    /**
     * FCM 토큰
     *
     * @return string
     * @throws Exception
     */
    private function getAccessToken(): string
    {
        $this->client->setAuthConfig(base_path("authjson/{$this->clientName}.json"));
        $this->client->addScope(env('FCM_CREDENTIAL_SCOPE'));
        $this->client->fetchAccessTokenWithAssertion();
        return preg_replace('/\.\./', '', $this->client->getAccessToken()['access_token']);
    }

    /**
     * 단건 발송
     *
     * @param string $token
     * @return Response
     */
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

    /**
     * 헤더 읽기
     * 내부 발송 메서드를 이용할 수 없는 조건에서 수동 발송 로직을 위해 헤더 조각 리턴
     *
     * @return array
     */
    public function getHeader(): array
    {
        return $this->header;
    }

    /**
     * FCM endpoint
     * 내부 발송 send 메서드를 이용할 수 없는 조건에서 수동 발송 로직을 위해 url 리턴
     *
     * @return string
     */
    public function getFcmUrl(): string
    {
        return $this->fcmUrl;
    }

    /**
     * 수신자 토큰 포함한 본문
     * 내부 발송 메서드를 이용할 수 없는 조건에서 수동 발송 로직을 위해 본문 리턴
     *
     * @param string $token
     * @return array[]
     */
    public function getBody(string $token): array
    {
        return [
            "message" => [
                "token" => $token,
                "notification" => $this->notification,
            ],
        ];
    }

    /**
     * 수신자들 토큰을 포함한 본문
     * 내부 발송 메서드를 이용할 수 없는 조건에서 수동 발송 로직을 위한 대량 사용자들 포함한 본문 리턴
     *
     * @param array $tokens
     * @return array
     */
    public function getBulkBody(array $tokens): array
    {
        $bodies = [];

        foreach ($tokens as $token) {
            $body = [
                "message" => [
                    "token" => $token,
                    "notification" => $this->notification,
                ],
            ];

            $bodies[] = $body;
        }

        return $bodies;
    }
}
