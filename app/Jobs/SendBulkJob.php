<?php

namespace App\Jobs;

use App\Models\PushHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Redis\LimiterTimeoutException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\Pool;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class SendBulkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $clientSet;

    /**
     * Create a new job instance.
     * params [ fcm 발송을 위한 endpoint, 헤더, 본문 ]
     * params + [ 내역 저장을 위한 수신자목록, 클라이언트 아이디, 푸시 본문 내역 ID ]
     *
     * @return void
     */
    public function __construct(array $params)
    {
        $this->clientSet = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws LimiterTimeoutException
     */
    public function handle()
    {
        Redis::throttle(env('APP_NAME'))->allow(100)->every(10)->then(function() {

            try{

                $results = Http::pool(fn (Pool $pool) => array_map(function($body) use ($pool) {
                    return $pool->withHeaders($this->clientSet['header'])
                        ->post($this->clientSet['fcmUrl'], $body);
                }, $this->clientSet['bodies']));

                $statuses = [];
                foreach ($results as $idx => $result) {

                    Log::info($result);

                    $statuses[] = [
                        'token' => $this->clientSet['tokens'][$idx],
                        'status' => $result->status(),
                    ];
                }

                PushHistory::create([
                    'client_id' => $this->clientSet['client_id'],
                    'push_id' => $this->clientSet['push_id'],
                    'status' => json_encode($statuses),
                ]);

            } catch (\Exception $e){
                Log::error($e->getMessage());
            }

        }, function () {
            $this->release(2);
        });
    }
}
