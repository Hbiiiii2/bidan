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
        Schema::table('services', function (Blueprint $table) {
            $table->date('available_from_date')->nullable()->after('available_date');
            $table->date('available_until_date')->nullable()->after('available_from_date');
            $table->time('available_start_time')->nullable()->after('available_until_date');
            $table->time('available_end_time')->nullable()->after('available_start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'available_from_date',
                'available_until_date',
                'available_start_time',
                'available_end_time',
            ]);
        });
    }
};
