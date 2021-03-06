<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\Api\V1\DefaultRequest;
use App\Http\Requests\Api\V1\EachRequest;
use App\Services\Api\V1\SendService;
use Illuminate\Http\Response;

class SendController extends Controller
{
    private SendService $sendService;

    /**
     * @param SendService $sendService
     */
    public function __construct(SendService $sendService)
    {
        $this->sendService = $sendService;
    }

    /**
     * @param DefaultRequest $request
     * @return Response
     */
    public function sendDefault(DefaultRequest $request): Response
    {
        return $this->response($this->sendService->sendDefault($request));
    }

    public function sendEach(EachRequest $request): Response
    {
        return $this->response($this->sendService->sendEach($request));
    }
}
