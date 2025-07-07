<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class APIFormRequest extends FormRequest
{
    /**
     * Формат вывода ошибок валидации для API
     *
     * @param Validator $validator
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        if (!str_starts_with($this->path(), 'api/')) {
            parent::failedValidation($validator);
        }

        throw new HttpResponseException(
            response()->json(
                [
                    'errors' => [
                        'fields' => $validator->errors(),
                    ],
                ],
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                [],
            )
        );
    }

}
