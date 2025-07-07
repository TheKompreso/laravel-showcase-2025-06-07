<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\BookingSlot;

class NoSlotOverlap implements Rule
{
    protected $start_time;
    protected $end_time;
    protected $exclude_slot_id;

    /**
     * Создайте правило с необходимыми параметрами.
     *
     * @param string $start_time - время начала
     * @param string $end_time - время конца
     * @param int|null $exclude_slot_id - ID слота, который исключается при обновлении (опционально)
     */
    public function __construct($start_time, $end_time, $exclude_slot_id = null)
    {
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->exclude_slot_id = $exclude_slot_id;
    }

    public function passes($attribute, $value): bool
    {
        // Проверяем пересечения
        return !self::checkOverlapInDatabase($this->start_time, $this->end_time, $this->exclude_slot_id);
    }

    public function message(): string
    {
        return 'Выбранное время пересекается с существующим слотом.';
    }

    public static function checkOverlapInDatabase($start_time, $end_time, $exclude_slot_id = null)
    {
        return BookingSlot::where(function ($query) use ($end_time, $start_time) {
            $query->whereBetween('start_time', [$start_time, $end_time])
                ->orWhereBetween('end_time', [$start_time, $end_time])
                ->orWhere(function ($q) use ($start_time, $end_time) {
                    $q->where('start_time', '<=', $start_time)
                        ->where('end_time', '>=', $end_time);
                });
        })
            ->when($exclude_slot_id, function ($query) use ($exclude_slot_id) {
                return $query->where('id', '<>', $exclude_slot_id);
            })
            ->exists();
    }
}
