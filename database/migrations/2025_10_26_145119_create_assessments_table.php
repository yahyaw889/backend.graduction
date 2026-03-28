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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('image_path')->nullable(); 
            $table->integer('risk_percentage')->default(0);
            $table->enum('recommendation', ['take_precautions' , 'take_precautions_and_see_doctor' , 'see_doctor'])->nullable();
            $table->text('report_text')->nullable();
            $table->text('symptoms_text')->nullable();
            $table->json('symptoms_selected')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->text('reason')->nullable();
            $table->enum('model_type' , ['model_image' , 'model_text' , 'both' , 'other'])->default('other');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
