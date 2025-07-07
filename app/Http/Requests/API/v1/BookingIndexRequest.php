<?php

namespace App\Http\Requests\API\v1;

use App\Http\Requests\APIFormRequest;

class BookingIndexRequest extends APIFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'per_page' => [
                'nullable',
                'integer',
                'min:10',
                'max:100',
            ],
        ];
    }
}
