<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\Api\V1\DefaultRequest;
use App\Services\Api\V1\SendService;
use Illuminate\Http\Response;

class SendController extends Controller
{
    private SendService $sendService;

    public function __construct(SendService $sendService)
    {
        $this->sendService = $sendService;
    }

    public function sendDefault(DefaultRequest $request): Response
    {
        return $this->response($this->sendService->sendDefault($request));
    }
}
