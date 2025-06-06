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
        Schema::table('users', function (Blueprint $table) {
             $table->string('FirstName')->after('id');
            $table->string('LastName')->after('FirstName');
            $table->enum('role', ['admin', 'user'])->default('user')->after('LastName');
            $table->dropColumn('name'); // Ukloni name kolonu
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->dropColumn(['FirstName', 'LastName', 'role']);
        });
    }
};
