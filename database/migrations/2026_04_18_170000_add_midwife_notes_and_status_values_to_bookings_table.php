<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->text('midwife_notes')->nullable()->after('notes');
        });

        DB::statement("ALTER TABLE `bookings` MODIFY `status` ENUM('pending','paid','completed','canceled','confirmed','declined') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `bookings` MODIFY `status` ENUM('pending','paid','completed','canceled') NOT NULL DEFAULT 'pending'");

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('midwife_notes');
        });
    }
};
