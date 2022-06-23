<?php

namespace App\Jobs;

use App\Models\PushHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * 1명씩 발송을 위해 기존에 만든 로직
 * 현재는 쓰지 않고 일부 변경된 내용 있으므로 사용전 검토 필요
*/
class SendOneJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $clientSet;

    /**
     * Create a new job instance.
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
     */
    public function handle()
    {
        Redis::throttle(env('APP_NAME'))->allow(100)->every(10)->then(function() {

            try{
                $result = Http::withHeaders($this->clientSet['header'])->post($this->clientSet['fcmUrl'], $this->clientSet['body']);

                PushHistory::create([
                    'client_id' => $this->clientSet['client_id'],
                    'push_id' => $this->clientSet['push_id'],
                    'token' => $this->clientSet['token'],
                    'status' => $result->status(),
                ]);
            } catch (\Exception $e){
                Log::error($this->clientSet['token'].':::'.$e->getMessage());
            }

        }, function () {
            $this->release(2);
        });
    }
}
