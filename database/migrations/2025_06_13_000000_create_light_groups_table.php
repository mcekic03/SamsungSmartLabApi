<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('light_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->string('name');
            $table->unsignedTinyInteger('group_index'); // npr. 0-4
            $table->string('status')->default('off'); // on/off
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('light_groups');
    }
}; 