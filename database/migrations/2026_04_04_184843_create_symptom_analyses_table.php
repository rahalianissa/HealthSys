<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('symptom_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->nullable()->constrained();
            $table->text('symptoms');
            $table->string('suggested_specialty');
            $table->string('suggested_doctor_id')->nullable();
            $table->string('urgency_level'); // low, medium, high, emergency
            $table->text('advice');
            $table->text('recommendations')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('symptom_analyses');
    }
};