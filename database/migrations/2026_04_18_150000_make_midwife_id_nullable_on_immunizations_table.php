<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE `immunizations` DROP FOREIGN KEY `immunizations_midwife_id_foreign`');
        DB::statement('ALTER TABLE `immunizations` MODIFY `midwife_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `immunizations` ADD CONSTRAINT `immunizations_midwife_id_foreign` FOREIGN KEY (`midwife_id`) REFERENCES `users`(`id`) ON DELETE SET NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `immunizations` DROP FOREIGN KEY `immunizations_midwife_id_foreign`');
        DB::statement('ALTER TABLE `immunizations` MODIFY `midwife_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `immunizations` ADD CONSTRAINT `immunizations_midwife_id_foreign` FOREIGN KEY (`midwife_id`) REFERENCES `users`(`id`) ON DELETE CASCADE');
    }
};
