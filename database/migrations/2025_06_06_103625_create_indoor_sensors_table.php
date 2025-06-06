<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('indoor_sensors', function (Blueprint $table) {
            $table->id();
             $table->integer('sensor_id')->nullable();
            $table->float('temperature', 5, 2)->nullable()->comment('Temperatura u °C');
            $table->float('co_level', 5, 2)->nullable()->comment('Nivo CO');
            $table->float('pressure', 8, 2)->nullable()->comment('Pritisak u Pa');
            $table->float('humidity', 5, 2)->nullable()->comment('Relativna vlažnost u %');
            $table->timestamp('sensor_timestamp')->nullable()->comment('Timestamp senzora');
           
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indoor_sensors');
    }
};
