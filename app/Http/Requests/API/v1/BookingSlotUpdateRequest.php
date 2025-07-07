<?php

namespace App\Http\Requests\API\v1;

use App\Http\Requests\APIFormRequest;
use App\Rules\NoSlotOverlap;

class BookingSlotUpdateRequest extends APIFormRequest
{
    public function wantsJson()
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'start_time' => [
                'required',
                'date',
                'before:end_time',
            ],
            'end_time' => [
                'required',
                'date',
                new NoSlotOverlap(
                    $this->input('start_time'),
                    $this->input('end_time'),
                    $this->route('slot')->id),
            ],
        ];
    }
}
