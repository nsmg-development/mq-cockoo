<?php

namespace App\Jobs;

use App\Models\PushHistory;
use App\Services\Api\V1\FCMClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

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
        Redis::throttle(env('APP_NAME'))->allow(2)->every(1)->then(function() {

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
