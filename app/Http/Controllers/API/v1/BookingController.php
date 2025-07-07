<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Requests\API\v1\BookingIndexRequest;
use App\Http\Requests\API\v1\BookingSlotStoreRequest;
use App\Http\Requests\API\v1\BookingSlotUpdateRequest;
use App\Http\Requests\API\v1\BookingStoreRequest;
use App\Http\Resources\v1\BookingCollection;
use App\Http\Resources\v1\BookingResource;
use App\Models\Booking;
use App\Models\BookingSlot;
use Illuminate\Support\Facades\Auth;

class BookingController
{
    public function index(BookingIndexRequest $request): BookingCollection
    {
        $per_page = $request->get('per_page', 10);

        return BookingCollection::make(Booking::where('user_id', Auth::id())->paginate($per_page));
    }

    public function store(BookingStoreRequest $request): BookingResource
    {
        $booking = Booking::create([
            'user_id' => Auth::id(),
        ]);

        $slots = $request->input('slots');
        foreach ($slots as $slot) {
            $booking->bookingSlots()->create([
                'start_time' => $slot['start_time'],
                'end_time' => $slot['end_time'],
            ]);
        }

        return BookingResource::make($booking);
    }

    public function updateSlot(Booking $booking, BookingSlot $slot, BookingSlotUpdateRequest $request): BookingResource
    {
        if ($booking->user_id != Auth::id()) {
            abort(403, 'Access denied.');
        }

        $slot->update([
            'start_time' => $request['start_time'],
            'end_time' => $request['end_time'],
        ]);

        return BookingResource::make($booking);
    }

    public function addSlot(Booking $booking, BookingSlotStoreRequest $request): BookingResource
    {
        if ($booking->user_id != Auth::id()) {
            abort(403, 'Access denied.');
        }

        $booking->bookingSlots()->create([
            'start_time' => $request['start_time'],
            'end_time' => $request['end_time'],
        ]);

        return BookingResource::make($booking);
    }

    public function destroy(Booking $booking): ?bool
    {
        if ($booking->user_id != Auth::id()) {
            abort(403, 'Access denied.');
        }

        return $booking->delete();
    }
}
