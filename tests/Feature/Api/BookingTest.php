<?php

    namespace Tests\Feature\Api;

    use App\Models\User;
    use Carbon\Carbon;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Tests\TestCase;

    class BookingTest extends TestCase
    {
        use RefreshDatabase;

        public function testCreateWithSlots(): void
        {
            $slots = [
                [
                    "start_time" => "2025-06-25 12:00:00",
                    "end_time" => "2025-06-25 13:00:00"
                ],
                [
                    "start_time" => "2025-06-25 13:30:00",
                    "end_time" => "2025-06-25 14:30:00"
                ]
            ];

            $user = User::factory()->create();

            $response = $this->withHeader('Authorization', 'Bearer ' . $user->api_token)
                ->postJson(route('bookings.store'), ['slots' => $slots]);

            $response->assertStatus(200);
            $response->assertJsonFragments($slots);
        }

        public function testDoesntAddSlotWithConflict(): void
        {
            $slot = [
                "start_time" => "2025-06-25 11:30:00",
                "end_time" => "2025-06-25 12:30:00"
            ];

            $slotConflict = [
                "start_time" => "2025-06-25 12:00:00",
                "end_time" => "2025-06-25 13:00:00"
            ];

            $user = User::factory()->create();
            $booking = $user->bookings()->create();
            $booking->slots()->create($slot);

            $response = $this->withHeader('Authorization', 'Bearer ' . $user->api_token)
                ->postJson(route('bookings.add', $booking), $slotConflict);

            $response->assertStatus(422);
            $response->assertJsonPath('error', 'Choosed dates for slots are not free');
        }

        public function testDoesntUpdateSlotWithConflict(): void
        {
            $slots = [
                [
                    "start_time" => "2025-06-25 12:00:00",
                    "end_time" => "2025-06-25 13:00:00"
                ],
                [
                    "start_time" => "2025-06-25 13:30:00",
                    "end_time" => "2025-06-25 14:30:00"
                ]
            ];

            $slotConflict = [
                "start_time" => "2025-06-25 14:00:00",
                "end_time" => "2025-06-25 15:00:00"
            ];

            $user = User::factory()->create();
            $booking = $user->bookings()->create();
            $booking->slots()->createMany($slots);
            $booking->load('slots');


            $response = $this->withHeader('Authorization', 'Bearer ' . $user->api_token)
                ->patchJson(route('bookings.update', ['booking' => $booking, 'slot' => $booking->slots[0]]), $slotConflict);

            $response->assertStatus(422);
            $response->assertJsonPath('error', 'Choosed dates for slots are not free');
        }

        public function testSuccessUpdateSlot(): void
        {
            $slot = [
                "start_time" => "2025-06-25T10:00:00",
                "end_time" => "2025-06-25T11:00:00"
            ];

            $slots = [
                [
                    "start_time" => "2025-06-25T12:00:00",
                    "end_time" => "2025-06-25T13:00:00"
                ],
                [
                    "start_time" => "2025-06-25T13:30:00",
                    "end_time" => "2025-06-25T14:30:00"
                ]
            ];

            $user = User::factory()->create();
            $booking = $user->bookings()->create();
            $booking->slots()->createMany($slots);
            $booking->load('slots');

            $response = $this->withHeader('Authorization', 'Bearer ' . $user->api_token)
                ->patchJson(route('bookings.update', ['booking' => $booking, 'slot' => $booking->slots[0]]), $slot);

            $response->assertStatus(200);

            foreach (['start_time', 'end_time'] as $key)
                $response->assertJsonPath($key, Carbon::parse($slot[$key])->format('Y-m-d\TH:i:s.u\Z'));
        }
    }
