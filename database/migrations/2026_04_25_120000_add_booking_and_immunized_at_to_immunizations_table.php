<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('immunizations', function (Blueprint $table) {
            $table->foreignId('booking_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->dateTime('immunized_at')->nullable()->after('date');
        });
    }

    public function down(): void
    {
        Schema::table('immunizations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('booking_id');
            $table->dropColumn('immunized_at');
        });
    }
};
