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
            $table->json('image_path'); 
            $table->integer('risk_percentage')->default(0);
            $table->enum('recommendation', ['مراقبة_منزلية', 'استشارة_طبية', 'رعاية_طارئة']);
            $table->text('report_text');
            $table->enum('status', ['قيد_المراجعة', 'مكتمل', 'ملغى'])->default('قيد_المراجعة');
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
