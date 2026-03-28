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
        Schema::create('reminder_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reminder_id')->constrained()->onDelete('cascade');

            $table->date('date');
            $table->enum('action', ['skip', 'modify']); // إلغاء اليوم أو تعديله

            $table->time('new_time')->nullable(); // لو هتغير ميعاد اليوم فقط

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminder_exceptions');
    }
};
