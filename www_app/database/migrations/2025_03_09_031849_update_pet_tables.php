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

ALTER TABLE `session` 
DROP INDEX `session_user_id_index`,
DROP COLUMN `user_id`;

ALTER TABLE `session` 
ADD COLUMN `user_id` BIGINT(19) UNSIGNED NULL DEFAULT NULL AFTER `id`,
ADD INDEX `fk_user_id_idx` (`user_id` ASC);

ALTER TABLE `user` 
ADD COLUMN `phone` VARCHAR(16) NULL DEFAULT NULL AFTER `password`;

CREATE TABLE IF NOT EXISTS `pet_type` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `pet_breed` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pet_type_id` INT(10) UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_pet_type_id1_idx` (`pet_type_id` ASC),
  CONSTRAINT `fk_pet_type_id1`
    FOREIGN KEY (`pet_type_id`)
    REFERENCES `pet_type` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `pet_owner` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT(19) UNSIGNED NOT NULL,
  `pet_type_id` INT(10) UNSIGNED NOT NULL,
  `pet_breed_id` INT(10) UNSIGNED NULL DEFAULT NULL,
  `nickname` VARCHAR(255) NULL DEFAULT NULL,
  `year_of_birth` MEDIUMINT(9) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_user_id1_idx` (`user_id` ASC),
  INDEX `fk_pet_type_id2_idx` (`pet_type_id` ASC),
  INDEX `fk_pet_breed_id1_idx` (`pet_breed_id` ASC),
  CONSTRAINT `fk_user_id1`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_pet_type_id2`
    FOREIGN KEY (`pet_type_id`)
    REFERENCES `pet_type` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_pet_breed_id1`
    FOREIGN KEY (`pet_breed_id`)
    REFERENCES `pet_breed` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

ALTER TABLE `session` 
ADD CONSTRAINT `fk_user_id`
  FOREIGN KEY (`user_id`)
  REFERENCES `user` (`id`)
  ON DELETE RESTRICT
  ON UPDATE CASCADE;

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
        Schema::table('session', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['fk_user_id_idx']);
            $table->dropColumn('user_id');
        });

        Schema::table('session', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->index('user_id', 'session_user_id_index');
        });

        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('phone');
        });

        Schema::dropIfExists('pet_owner');
        Schema::dropIfExists('pet_breed');
        Schema::dropIfExists('pet_type');
    }
};
