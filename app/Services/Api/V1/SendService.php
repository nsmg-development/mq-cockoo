<?php

namespace App\Services\Api\V1;


use App\Http\Requests\Api\V1\DefaultRequest;
use App\Jobs\SendBulkJob;
use App\Models\Push;
use Illuminate\Support\Collection;

class SendService
{
    private FCMClient $client;
    private Push $push;

    public function __construct(FCMClient $client, Push $push)
    {
        $this->client   = $client;
        $this->push     = $push;
    }

    /**
     * FCM 토큰 목록, 제목, 내용 request
     * 해당 유저 목록에 대량 push 메시지 발송 queue에 저장
     *
     * @param DefaultRequest $request
     * @return Collection
     */
    public function sendDefault(DefaultRequest $request): Collection
    {

        $clientId = get_client_id();
        $this->client->client($clientId);
        $this->client->setNotification($request->get('title'), $request->get('body'));

        $push = $this->push->create([
            'client_id' => $clientId,
            'title'     => $request->get('title'),
            'body'      => $request->get('body'),
        ]);

        $param = [
            'fcmUrl'    => $this->client->getFcmUrl(),
            'header'    => $this->client->getHeader(),
            'bodies'    => $this->client->getBulkBody($request->get('tokens')),
            'tokens'    => $request->get('tokens'),
            'client_id' => $clientId,
            'push_id'   => $push->id,
        ];
        SendBulkJob::dispatch($param);

        return collect([]);
    }
}
