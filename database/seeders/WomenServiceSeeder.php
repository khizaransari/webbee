<?php

namespace Database\Seeders;

use App\Models\BreakTime;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class WomenServiceSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */

    public function womenService($serviceId)
    {
        $startDate = Carbon::now()->startOfDay();
        $endDate = $startDate->copy()->addDays(7);

        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            // Sunday is off
            if ($currentDate->dayOfWeek !== Carbon::SUNDAY) {
                // Men Haircut time slots
                if ($currentDate->copy()->addDays(2)->isSameDay(Carbon::now())) {
                    // The third day starting from now is a public holiday
                    $this->createPublicHolidayTimeSlots($currentDate, '08:00', '20:00', $serviceId);
                } else {
                    $this->createTimeSlots($currentDate, '08:00', '20:00', 10, $serviceId);
                }

                // Lunch break
                $this->createBreakTime($serviceId, '12:00', '13:00');

                // Cleaning break
                $this->createBreakTime($serviceId, '15:00', '16:00');
            }

            $currentDate->addDay();
        }
    }

    private function createTimeSlots($date, $startTime, $endTime, $duration, $serviceId)
    {
        $start = Carbon::parse($date->format('Y-m-d') . ' ' . $startTime);
        $end = Carbon::parse($date->format('Y-m-d') . ' ' . $endTime);

        while ($start->addMinutes($duration)->lte($end)) {
            TimeSlot::create([
                'date' => $date,
                'start_time' => $start->copy()->subMinutes($duration)->format('H:i:s'),
                'end_time' => $start->format('H:i:s'),
                'available' => 1,
                'service_id' => $serviceId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add the cleanup break time after each time slot
            $start->addMinutes(5);
        }
    }

    private function createPublicHolidayTimeSlots($date, $startTime, $endTime, $duration)
    {
        $start = Carbon::parse($date->format('Y-m-d') . ' ' . $startTime);
        $end = Carbon::parse($date->format('Y-m-d') . ' ' . $endTime);

        while ($start->addMinutes($duration)->lte($end)) {
            TimeSlot::create([
                'date' => $date,
                'start_time' => $start->copy()->subMinutes($duration)->format('H:i:s'),
                'end_time' => $start->format('H:i:s'),
                'available' => false, // Public holiday, time slots not available
            ]);

            // Add the cleanup break time after each time slot
            $start->addMinutes(5);
        }
    }

    private function createBreakTime($serviceId, $startTime, $endTime)
    {
        BreakTime::create([
            'service_id' => $serviceId,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);
    }


    public function run()
    {
        $serviceData =  [
            'name' => 'Woman Haircut',
            'number_of_clients' => 3,
            'buffer_time' => 10,
            'slot_duration' => 60,
            'consecutive_appointment_book' => 7,
            'available' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Generate records for three different services
        $serviceId = DB::table('services')->insertGetId($serviceData);
        $this->womenService($serviceId);
        $offDays =  [
            'title' => 'off',
            'service_id' => $serviceId,
            'day_of_week' => 'Sunday',
            'is_half_day' => 0,
        ];
        $serviceId = DB::table('off_days')->insertGetId($offDays);

    }
}
