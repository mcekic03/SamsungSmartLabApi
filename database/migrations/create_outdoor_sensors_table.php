<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('outdoor_sensors', function (Blueprint $table) {
            $table->id();
            $table->string('esp8266id');
            $table->string('software_version');
            $table->float('PMS_P0')->nullable();
            $table->float('PMS_P1')->nullable();
            $table->float('PMS_P2')->nullable();
            $table->float('BME280_temperature')->nullable();
            $table->float('BME280_pressure')->nullable();
            $table->float('BME280_humidity')->nullable();
            $table->integer('samples')->nullable();
            $table->integer('min_micro')->nullable();
            $table->integer('max_micro')->nullable();
            $table->integer('interval')->nullable();
            $table->integer('signal')->nullable();
            $table->timestamp('timestamp')->useCurrent();
        });
    }

};