<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NoOverlapBetweenSlots implements Rule
{
    protected $slots;

    protected $error_message = 'Временные слоты не должны пересекаться между собой.';

    public function __construct($slots)
    {
        $this->slots = $slots;
    }

    public function passes($attribute, $value)
    {
        $slots = collect($this->slots);

        foreach ($slots as $index => $slot) {
            $start = strtotime($slot['start_time']);
            $end = strtotime($slot['end_time']);

            foreach ($slots as $other_index => $other_slot) {
                if ($index === $other_index) {
                    continue;
                }

                $other_start = strtotime($other_slot['start_time']);
                $other_end = strtotime($other_slot['end_time']);

                if (
                    ($start < $other_end && $end > $other_start)
                ) {
                    return false;
                } elseif(NoSlotOverlap::checkOverlapInDatabase($slot['start_time'], $slot['end_time'])) {
                    $this->error_message = 'Выбранное время пересекается с существующим слотом.';
                    return false;
                }
            }
        }

        return true;
    }

    public function message(): string
    {
        return $this->error_message;
    }
}
