<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;

trait ResponseTemplate
{
    /**
     * API 결과 코드 생성 후 전달
     *
     * @param Collection $result
     * @return Response
     */
    public function response(Collection $result): Response
    {
        return response($this->makeResponse($result), $result->get('statusCode') ?? 200);
    }

    /**
     * @param $_result
     * @return array
     */
    public function makeResponse($_result): array
    {
        if ($_result->has('statusCode')) {
            $message = $_result->get('message') ?? $_result->get('msg');
            $result = $this->makeError($_result->get('statusCode'), $message);
        } else {
            $result = $this->makeSuccess($_result, '정상 처리 되었습니다.');
        }

        return $result;
    }

    /**
     * @param $_data
     * @param null $_message
     * @return array
     */
    private function makeSuccess($_data, $_message = null): array
    {
        $result['result'] = true;
        $result['statusCode'] = 200;
        $result['message'] = $_message;
        $result['data'] = $_data;

        return $result;
    }

    /**
     * @param $_statusCode
     * @param $_message
     * @return array
     */
    private function makeError($_statusCode, $_message): array
    {
        $name = 'Error';
        $statusCode_arr = [
            '200' => 'SUCCESS',
            '204' => 'NO CONTENT',
            '400' => 'BAD REQUEST',
            '401' => 'UNAUTHORIZED',
            '403' => 'FORBIDDEN',
            '404' => 'NOT FOUND',
            '405' => 'METHOD NOT ALLOWED',
            '409' => 'CONFLICT',
            '422' => 'UNPROCESSABLE ENTITY',
            '423' => 'LOCKED',
            '500' => 'INTERNAL SERVER ERROR',
        ];

        if (array_key_exists($_statusCode, $statusCode_arr)) {
            $code = $statusCode_arr[$_statusCode];
        } else {
            $code = 'Bad Request';
        }

        $result['result'] = false;
        if ($_statusCode == "200") {
            $name = 'Success';
            $result['result'] = true;
        }

        $result['statusCode'] = $_statusCode;
        $result['message'] = $_message;

        $result['error']['statusCode'] = $_statusCode;
        $result['error']['name'] = $name;
        $result['error']['message'] = $_message;
        $result['error']['code'] = $code;

        return $result;
    }
}
