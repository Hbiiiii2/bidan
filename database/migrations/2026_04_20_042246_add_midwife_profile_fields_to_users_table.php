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
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_photo')->nullable()->after('role');
            $table->string('hospital_institution')->nullable()->after('profile_photo');
            $table->text('address')->nullable()->after('hospital_institution');
            $table->year('career_start_year')->nullable()->after('address');
            $table->json('available_days')->nullable()->after('career_start_year'); // e.g., ["monday", "tuesday"]
            $table->time('available_start_time')->nullable()->after('available_days');
            $table->time('available_end_time')->nullable()->after('available_start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_photo',
                'hospital_institution',
                'address',
                'career_start_year',
                'available_days',
                'available_start_time',
                'available_end_time',
            ]);
        });
    }
};
