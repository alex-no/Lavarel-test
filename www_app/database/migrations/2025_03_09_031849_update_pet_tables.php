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
DROP INDEX `sessions_user_id_index`,
DROP COLUMN `user_id`;

ALTER TABLE `sessions` 
ADD COLUMN `user_id` BIGINT(19) UNSIGNED NULL DEFAULT NULL AFTER `id`,
ADD INDEX `fk_user_id_idx` (`user_id` ASC);

ALTER TABLE `users` 
ADD COLUMN `phone` VARCHAR(16) NULL DEFAULT NULL AFTER `password`;

CREATE TABLE IF NOT EXISTS `pet_type` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `pet_breed` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pet_type_id` INT(10) UNSIGNED NOT NULL,
  `name` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC),
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
  `year_of_birth` MEDIUMINT(4) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`nickname` ASC),
  INDEX `fk_pet_type_id2_idx` (`pet_type_id` ASC),
  INDEX `fk_pet_breed_id1_idx` (`pet_breed_id` ASC),
  INDEX `fk_user_id1_idx` (`user_id` ASC),
  CONSTRAINT `fk_pet_type_id2`
    FOREIGN KEY (`pet_type_id`)
    REFERENCES `pet_type` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_pet_breed_id1`
    FOREIGN KEY (`pet_breed_id`)
    REFERENCES `pet_breed` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_user_id1`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

ALTER TABLE `sessions` 
ADD CONSTRAINT `fk_user_id`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`)
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
        //
    }
};
