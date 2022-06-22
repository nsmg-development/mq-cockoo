<?php

namespace App\Services\Api\V1;


use App\Http\Requests\Api\V1\DefaultRequest;
use App\Jobs\SendOneJob;
use Illuminate\Support\Collection;
use Lcobucci\JWT\Configuration;

class SendService
{
    private FCMClient $client;

    public function __construct(FCMClient $client)
    {
        $this->client = $client;
    }

    public function sendDefault(DefaultRequest $request): Collection
    {

        $clientId = get_client_id();
        $this->client->client($clientId);
        $this->client->setNotification($request->get('title'), $request->get('body'));

        foreach ($request->get('tokens') as $token) {
            $param = [
                'fcmUrl' => $this->client->getFcmUrl(),
                'header' => $this->client->getHeader(),
                'body' => $this->client->getBody($token),
            ];
            SendOneJob::dispatch($param);
        }

        return collect([]);
    }
}
