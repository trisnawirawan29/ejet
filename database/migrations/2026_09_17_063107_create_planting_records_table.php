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
        Schema::create('planting_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planting_access_token_id');
            $table->string('name');
            $table->string('phone', 30);
            $table->string('email')->nullable();
            $table->string('organization')->nullable();
            $table->string('job_title')->nullable();
            $table->date('planted_at');
            $table->string('plant_type');
            $table->unsignedInteger('tree_count');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('location_name')->nullable();
            $table->string('photo_path');
            $table->timestamps();

            $table->index(['planted_at', 'plant_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planting_records');
    }
};
