<?php

namespace App\Services\Api\V1;


use App\Http\Requests\Api\V1\DefaultRequest;
use App\Jobs\SendOneJob;
use App\Models\Push;
use Illuminate\Support\Collection;

class SendService
{
    private FCMClient $client;
    private Push $push;

    public function __construct(FCMClient $client, Push $push)
    {
        $this->client = $client;
        $this->push = $push;
    }

    public function sendDefault(DefaultRequest $request): Collection
    {

        $clientId = get_client_id();
        $this->client->client($clientId);
        $this->client->setNotification($request->get('title'), $request->get('body'));

        $push = $this->push->create([
            'client_id' => $clientId,
            'title' => $request->get('title'),
            'body' => $request->get('body'),
        ]);

        foreach ($request->get('tokens') as $token) {
            $param = [
                'fcmUrl'    => $this->client->getFcmUrl(),
                'header'    => $this->client->getHeader(),
                'body'      => $this->client->getBody($token),
                'token' => $token,
                'client_id' => $clientId,
                'push_id'   => $push->id,
            ];
            SendOneJob::dispatch($param);
        }

        return collect([]);
    }
}
