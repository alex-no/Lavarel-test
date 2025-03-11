<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $sql = <<<SQL
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `sessions` 
DROP INDEX `session_user_id_index`,
DROP COLUMN `user_id`;

ALTER TABLE `sessions` 
ADD COLUMN `user_id` BIGINT(19) UNSIGNED NULL DEFAULT NULL AFTER `id`,
ADD INDEX `fk_user_id_idx` (`user_id` ASC);

ALTER TABLE `users` 
ADD COLUMN `phone` VARCHAR(16) NULL DEFAULT NULL AFTER `password`;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;

        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['fk_user_id_idx']);
            $table->dropColumn('user_id');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->index('user_id', 'session_user_id_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
        });

    }
};
