<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeSlotsTable extends Migration
{
    public function up()
    {
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('available')->default(true);
            $table->unsignedBigInteger('service_id');
            $table->timestamps();

            // $table->foreign('time_slot_id')->references('id')->on('time_slots')->onDelete('cascade');
        });

    }

    public function down()
    {
        Schema::dropIfExists('time_slots');
    }
}
