<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('immunization_recall_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vaccine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('immunization_id')->constrained()->cascadeOnDelete();
            $table->string('next_dose');
            $table->date('due_date');
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->unique(['child_id', 'vaccine_id', 'next_dose', 'due_date'], 'immunization_recall_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('immunization_recall_logs');
    }
};
