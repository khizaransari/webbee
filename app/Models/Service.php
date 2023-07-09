<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function timeSlots()
    {
        return $this->hasMany(TimeSlot::class);
    }

    public function offDays()
    {
        return $this->hasMany(OffDay::class);
    }

    public function bookings()
    {
        return $this->hasManyThrough(Booking::class, TimeSlot::class);
    }

    public function breaks()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function scopeActive($query)
    {
        return $query->where('available', 1);
    }
}
