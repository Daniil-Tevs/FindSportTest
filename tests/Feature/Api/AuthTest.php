<?php

    namespace Tests\Feature\Api;

    use App\Models\User;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Tests\TestCase;

    use Illuminate\Support\Str;

    class AuthTest extends TestCase
    {
        use RefreshDatabase;

        public function testForbiddenAccessWithoutApiToken(): void
        {
            $response = $this->getJson(route('bookings.index'));

            $response->assertStatus(401);
        }

        public function testForbiddenAccessWithWrongApiToken(): void
        {
            $apiToken = Str::random(14) . 'unique';

            $response = $this->withHeader('Authorization', 'Bearer ' . $apiToken)->get(route('bookings.index'));

            $response->assertStatus(401);
        }

        public function testSuccessAccessWithApiToken(): void
        {
            $user = User::factory()->create();

            $response = $this->withHeader('Authorization', 'Bearer ' . $user->api_token)->get(route('bookings.index'));

            $response->assertStatus(200);
            $response->assertJson([]);
        }
    }
