<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('number_of_clients')->default(1);
            $table->unsignedBigInteger('buffer_time');
            $table->unsignedBigInteger('slot_duration');
            $table->integer('consecutive_appointment_book')->nullable();
            $table->boolean('available')->default(true);
            // Add any other relevant fields for services
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('services');
    }
}
