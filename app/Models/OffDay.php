<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OffDay extends Model
{
    protected $table = 'off_days';

    protected $guarded = [
        'id',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
