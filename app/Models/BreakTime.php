<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakTime extends Model
{
    protected $table = 'breaks';

    protected $guarded = ['id'];


    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
