<?php

namespace App\Http\Controllers\Api;

use App\Helper\Helpers;
use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Models\BreakTime;
use App\Models\OffDay;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->date;
        $services = Service::active()->with(['timeSlots', 'offDays', 'breaks', 'bookings'])->get();
        return response()->json([
            'services' => $services
        ], 200);

    }

    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'time_slot_id' => 'required|exists:time_slots,id',
            'people' => 'required',
            // 'people.*.email' => 'required|email',
            // 'people.*.first_name' => 'required',
            // 'people.*.last_name' => 'required',
        ]);


        $peopleFields = ['email', 'first_name', 'last_name'];
        $people = isset($request->people) ? json_decode($request->people, true) : [];
        if(count($people) > 0) {
            $is_variant = true;
            foreach ($people as $item) {
                foreach ($peopleFields as $key) {
                    if (!isset($item[$key])) {
                        $validator->after(function ($validator) use ($key) {
                            $validator->errors()->add(
                                'people',
                                "The '$key' key is missing in one of the people."
                            );
                        });
                    }
                }
            }
        }


        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        // Check if the requested service exists and is available
        $service = Service::find($request['service_id']);
        if (!$service->available) {
            return response()->json(['message' => 'Requested service is not available'], 400);
        }

        // Check if the requested time slot exists and is available
        $timeSlot = TimeSlot::findOrFail($request['time_slot_id']);
        if (!$timeSlot->available) {
            return response()->json(['message' => 'Requested time slot is not available'], 400);
        }

        // Check if the requested time slot falls within breaks
        $breaks = BreakTime::where('service_id', $request['service_id'])->get();
        foreach ($breaks as $break) {
            $breakStartTime = Carbon::parse($timeSlot->date . ' ' . $break->start_time);
            $breakEndTime = Carbon::parse($timeSlot->date . ' ' . $break->end_time);
            if ($timeSlot->start_time >= $breakStartTime && $timeSlot->end_time <= $breakEndTime) {
                return response()->json(['message' => 'Requested time slot falls within a break time'], 400);
            }
        }

        // Check if the requested time slot is on an off day
        $carbonDate = Carbon::parse($timeSlot->date);
        $dayName = $carbonDate->format('l');
        $offDay = OffDay::where('day_of_week', $dayName)->first();
        if ($offDay) {
            return response()->json(['message' => 'Requested time slot is on an off day'], 400);
        }

        // Check if the requested time slot exceeds the buffer time
        $bufferTime = $service->buffer_time;
        $startTime = Carbon::parse($timeSlot->date . ' ' . $timeSlot->start_time);
        $endTime = Carbon::parse($timeSlot->date . ' ' . $timeSlot->end_time);
        if ($startTime->diffInMinutes() < $bufferTime || $endTime->diffInMinutes() < $bufferTime) {
            return response()->json(['message' => 'Requested time slot is not valid'], 400);
        }

        // Create the booking
        $booking = new Booking();
        $booking->time_slot_id = $request['time_slot_id'];
        $booking->save();

        // Create booking participants
        foreach ($people as $person) {
            $booking->participants()->create([
                'email' => $person['email'],
                'first_name' => $person['first_name'],
                'last_name' => $person['last_name'],
            ]);
        }

        // Update the availability of the time slot
        $timeSlot->available = false;
        $timeSlot->save();

        return response()->json([
            'message' => 'Booking created successfully',
            'booking_id' => $booking->id
        ], 201);
    }
}
