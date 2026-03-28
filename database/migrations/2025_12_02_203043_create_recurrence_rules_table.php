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
        Schema::create('recurrence_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reminder_id')->constrained()->onDelete('cascade');

            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'yearly', 'custom']);

            $table->integer('interval')->default(1); // كل كام يوم، كل كام شهر...

            // weekly
            $table->json('days_of_week')->nullable(); // [1,3,5]

            // monthly
            $table->json('days_of_month')->nullable(); // [1,2,3]

            // yearly
            $table->json('months_of_year')->nullable(); // [1,9] = يناير وسبتمبر

            // وقت التذكير
            $table->time('time')->nullable();

            // تاريخ البداية والنهاية
            $table->date('start_date');
            $table->date('end_date')->nullable();

            $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurrence_rules');
    }
};
