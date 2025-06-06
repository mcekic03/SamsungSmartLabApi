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
        Schema::table('indoor_sensors', function (Blueprint $table) {
            $table->timestamp('sensor_timestamp')->nullable()->comment('Timestamp senzora');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indoor_sensors', function (Blueprint $table) {
            $table->dropColumn('sensor_timestamp');
        });
    }
};
