<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Support\Str;

use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingAPITest extends TestCase
{
    use RefreshDatabase;

    protected function getTrueAuthToken(): string
    {
        return User::first()->api_token;
    }

    protected function getFalseAuthToken(): string
    {
        return Str::random(16);
    }

    /**
     * Тест: создание бронирования с несколькими слотами.
     */
    public function testCreateBookingWithMultipleSlots()
    {
        $this->seed();

        $token = $this->getTrueAuthToken();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/bookings', [
                'slots' => [
                    ['start_time' => '2024-10-01 10:00', 'end_time' => '2024-10-01 11:00'],
                    ['start_time' => '2024-10-01 12:00', 'end_time' => '2024-10-01 13:00'],
                ],
            ]);

        $response->assertStatus(201);
    }

    /**
     * Тест: добавление слота с конфликтом — ошибка.
     */
    public function testAddSlotWithConflict()
    {
        $this->testCreateBookingWithMultipleSlots(); // Создаём запись для теста

        $token = $this->getTrueAuthToken();

        // Предположим, что слот конфликтует с существующим
        $bookingId = 1; // ID существующего бронирования
        $conflictingSlot = ['start_time' => '2024-10-01 10:30', 'end_time' => '2024-10-01 11:30'];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/v1/bookings/{$bookingId}/slots", $conflictingSlot);

        $response->assertStatus(422)
            ->assertJsonPath('errors.fields.end_time.0', 'Выбранное время пересекается с существующим слотом.');
    }

    /**
     * Тест: обновление слота — успешно.
     */
    public function testUpdateSlotSuccess()
    {
        $this->testCreateBookingWithMultipleSlots(); // Создаём запись для теста

        $token = $this->getTrueAuthToken();

        $bookingId = 1;
        $slotId = 1; // существующий слот

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->patchJson("/api/v1/bookings/{$bookingId}/slots/{$slotId}", [
                'start_time' => '2024-10-01 14:00',
                'end_time' => '2024-10-01 15:00',
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $slotId,
                'start_time' => '2024-10-01 14:00',
            ]);
    }

    /**
     * Тест: обновление несуществующего слота — ошибка.
     */
    public function testUpdateSlotFailure()
    {
        $this->seed();

        $token = $this->getTrueAuthToken();
        $bookingId = 1;
        $slotId = 999; // несуществующий слот

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->patchJson("/api/v1/bookings/{$bookingId}/slots/{$slotId}", [
                'start_time' => '2024-10-01 14:00',
                'end_time' => '2024-10-01 15:00',
            ]);

        $response->assertStatus(404);
    }

    /**
     * Тест: запрос без токена — отклоняется.
     */
    public function testRequestWithoutTokenIsRejected()
    {
        $this->seed();

        $response = $this->postJson('/api/v1/bookings', [
            'slots' => [
                ['start_time' => '2024-10-01 10:00', 'end_time' => '2024-10-01 11:00'],
            ],
        ]);

        $response->assertStatus(401); // Unauthorized
    }
}
