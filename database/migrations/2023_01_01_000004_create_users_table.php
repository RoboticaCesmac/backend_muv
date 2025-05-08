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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles');
            $table->string('gender')->nullable();
            $table->decimal('total_km_driven', 10, 2)->nullable();
            $table->decimal('total_carbon_footprint', 10, 2)->nullable();
            $table->decimal('total_points', 10, 2)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_first_login')->default(true);
            $table->foreignId('avatar_id')->nullable()->constrained('user_avatars');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
}; 