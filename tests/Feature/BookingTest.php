<?php

use App\Models\Service;
use App\Models\TimeSlot;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * @test
     */
    public function test_bookings_list_api()
    {
        $response = $this->get('/api/bookings/{date}');

        $response->assertStatus(200)
            ->assertJsonStructure(['services']);
    }

    /**
     * @test
     */
    public function test_booking_or_appointment_api_with_valid_data()
    {
        // Retrieve the seeded service and time slot for testing
        $service = Service::first();
        $timeSlot = TimeSlot::first();

        // Create the test data
        $data = [
            'service_id' => $service->id,
            'time_slot_id' => $timeSlot->id,
            'people' => '[{"email":"test@example.com","first_name":"John","last_name":"Doe"}]',
        ];

        $response = $this->postJson('/api/bookings', $data);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Booking created successfully',
                'booking_id' => true // You can assert against the booking ID returned in the response
            ]);

        // Assert that the booking is created in the database with the provided data
        $this->assertDatabaseHas('bookings', [
            'time_slot_id' => $timeSlot->id // Use the ID of the seeded time slot
        ]);

        // Assert that the time slot availability is updated to false
        $this->assertDatabaseHas('time_slots', [
            'id' => $timeSlot->id,
            'available' => false
        ]);

        // Assert that the booking participants are created with the correct information
        $this->assertDatabaseHas('booking_participants', [
            'email' => 'test@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);
    }

    public function testStoreWithInvalidData()
    {
        // Test with invalid data that should fail validation

        $data = [
            'service_id' => 999, // Invalid service ID
            'time_slot_id' => 999, // Invalid time slot ID
            'people' => '', // Empty people data
        ];

        $response = $this->postJson('/api/bookings', $data);

        $response->assertStatus(403)
            ->assertJsonStructure(['errors']);
    }
}
