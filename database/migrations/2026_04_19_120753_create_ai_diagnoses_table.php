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
        Schema::create('ai_diagnoses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('image_path');
            $table->integer('patient_age')->nullable();
            $table->string('patient_gender')->nullable();
            $table->text('reported_symptoms')->nullable();
            $table->integer('symptoms_duration_days')->nullable();
            
            // AI Response Data
            $table->string('diagnosis')->nullable();
            $table->integer('confidence_percentage')->nullable();
            $table->json('symptoms_detected')->nullable();
            $table->text('recommendation')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_diagnoses');
    }
};
