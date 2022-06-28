<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class DefaultRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return !!get_client_id();
    }

    /**
     * Get the validation rules that apply to the request.
     * tokens: 수신자 fcm토큰 목록
     *      token: 수신자 fcm 토큰.
     * 제목, 본문
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            "tokens" => "required|array",
                "tokens.*" => "required|string",
            "title" => "required|string",
            "body" => "required|string",
        ];
    }
}
