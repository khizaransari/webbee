<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Generate records for three different services
        for ($i = 0; $i < 3; $i++) {
            $serviceData = $this->generateServiceData($faker);
            $serviceId = DB::table('services')->insertGetId($serviceData);

            $timeSlots = $this->generateTimeSlots($faker, $serviceId, 10);
            DB::table('time_slots')->insert($timeSlots);

            $offDays = $this->generateOffDays($faker, $serviceId, 5);
            DB::table('off_days')->insert($offDays);

            $bookings = $this->generateBookings($faker, $serviceId, 3);
            DB::table('bookings')->insert($bookings);

            $bookingParticipants = $this->generateBookingParticipants($faker, 2);
            DB::table('booking_participants')->insert($bookingParticipants);

            $breaks = $this->generateBreaks($faker, $serviceId, 4);
            DB::table('breaks')->insert($breaks);
        }
    }

    /**
     * Generate data for a service.
     *
     * @param  \Faker\Generator  $faker
     * @return array
     */
    private function generateServiceData($faker)
    {
        return [
            'name' => $faker->word,
            'number_of_clients' => $faker->randomDigitNotNull,
            'buffer_time' => $faker->dateTime,
            'duration' => $faker->dateTime,
            'available' => $faker->boolean,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Generate time slots for a service.
     *
     * @param  \Faker\Generator  $faker
     * @param  int  $serviceId
     * @param  int  $count
     * @return array
     */
    private function generateTimeSlots($faker, $serviceId, $count)
    {
        $timeSlots = [];
        for ($i = 0; $i < $count; $i++) {
            $timeSlots[] = [
                'date' => $faker->date,
                'start_time' => $faker->time,
                'end_time' => $faker->time,
                'consecutive_appointment_book' => $faker->randomDigit,
                'available' => $faker->boolean,
                'service_id' => $serviceId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        return $timeSlots;
    }

    /**
     * Generate off days for a service.
     *
     * @param  \Faker\Generator  $faker
     * @param  int  $serviceId
     * @param  int  $count
     * @return array
     */
    private function generateOffDays($faker, $serviceId, $count)
    {
        $offDays = [];
        for ($i = 0; $i < $count; $i++) {
            $offDays[] = [
                'title' => $faker->word,
                'service_id' => $serviceId,
                'day_of_week' => $faker->randomElement(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
                'is_half_day' => $faker->boolean,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        return $offDays;
    }

    /**
     * Generate bookings for a service.
     *
     * @param  \Faker\Generator  $faker
     * @param  int  $serviceId
     * @param  int  $count
     * @return array
     */
    private function generateBookings($faker, $serviceId, $count)
    {
        $bookings = [];
        for ($i = 0; $i < $count; $i++) {
            $bookings[] = [
                'time_slot_id' => $faker->randomDigit,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        return $bookings;
    }

    /**
     * Generate booking participants.
     *
     * @param  \Faker\Generator  $faker
     * @param  int  $count
     * @return array
     */
    private function generateBookingParticipants($faker, $count)
    {
        $bookingParticipants = [];
        for ($i = 0; $i < $count; $i++) {
            $bookingParticipants[] = [
                'booking_id' => $faker->randomDigit,
                'email' => $faker->email,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        return $bookingParticipants;
    }

    /**
     * Generate breaks for a service.
     *
     * @param  \Faker\Generator  $faker
     * @param  int  $serviceId
     * @param  int  $count
     * @return array
     */
    private function generateBreaks($faker, $serviceId, $count)
    {
        $breaks = [];
        for ($i = 0; $i < $count; $i++) {
            $breaks[] = [
                'service_id' => $serviceId,
                'start_time' => $faker->time,
                'end_time' => $faker->time,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        return $breaks;
    }
}
