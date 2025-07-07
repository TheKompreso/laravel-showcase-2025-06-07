<?php

namespace App\Http\Requests\API\v1;

use App\Http\Requests\APIFormRequest;
use App\Rules\NoOverlapBetweenSlots;

class BookingStoreRequest extends APIFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'slots' => [
                'required',
                'array',
                'min:1',
                new NoOverlapBetweenSlots($this->input('slots')),
            ],
            'slots.*.start_time' => [
                'required',
                'date',
                'before:slots.*.end_time',
            ],
            'slots.*.end_time' => [
                'required',
                'date',
            ],
        ];
    }
}
